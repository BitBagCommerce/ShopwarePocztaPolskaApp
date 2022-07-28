<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Controller;

use BitBag\ShopwareAppSystemBundle\Model\Action\ActionInterface;
use BitBag\ShopwarePocztaPolskaApp\Api\DocumentApiServiceInterface;
use BitBag\ShopwarePocztaPolskaApp\Api\PackageApiServiceInterface;
use BitBag\ShopwarePocztaPolskaApp\Exception\ConfigException;
use BitBag\ShopwarePocztaPolskaApp\Exception\ConfigNotFoundException;
use BitBag\ShopwarePocztaPolskaApp\Exception\LabelException;
use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderAddressException;
use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderException;
use BitBag\ShopwarePocztaPolskaApp\Exception\PackageException;
use BitBag\ShopwarePocztaPolskaApp\Exception\StreetCannotBeSplitException;
use BitBag\ShopwarePocztaPolskaApp\Factory\FeedbackResponseFactoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Finder\OrderFinderInterface;
use BitBag\ShopwarePocztaPolskaApp\Provider\Defaults;
use BitBag\ShopwarePocztaPolskaApp\Repository\ConfigRepositoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\ApiResolverInterface;
use Symfony\Component\HttpFoundation\Response;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Entity\OrderDelivery\OrderDeliveryEntity;
use Vin\ShopwareSdk\Data\Filter\EqualsFilter;
use Vin\ShopwareSdk\Factory\RepositoryFactory;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class CreatePackageController
{
    public function __construct(
        private OrderFinderInterface $orderFinder,
        private FeedbackResponseFactoryInterface $feedbackResponseFactory,
        private ApiResolverInterface $apiResolver,
        private PackageApiServiceInterface $packageApiService,
        private ConfigRepositoryInterface $configRepository,
        private RepositoryInterface $orderDeliveryRepository,
        private DocumentApiServiceInterface $documentApiService
    ) {
    }

    public function create(ActionInterface $action, Context $context): Response
    {
        $orderId = $action->getData()->getIds()[0] ?? null;
        $shopId = $action->getSource()->getShopId();

        try {
            $order = $this->orderFinder->getWithAssociations($orderId, $context);
        } catch (OrderException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        $shippingMethod = $order->deliveries?->first()?->shippingMethod ?? null;
        if (null === $shippingMethod) {
            return $this->feedbackResponseFactory->createError(
                'bitbag.shopware_poczta_polska_app.order.shipping_method.not_found'
            );
        }

        $technicalName = $shippingMethod->getTranslated()['customFields']['technical_name'] ?? null;
        if (Defaults::SHIPPING_KEY !== $technicalName) {
            return $this->feedbackResponseFactory->createError(
                'bitbag.shopware_poczta_polska_app.order.shipping_method.not_polish_post'
            );
        }

        try {
            $config = $this->configRepository->getByShopIdAndSalesChannelId($shopId, $order->salesChannelId);
        } catch (ConfigException | ConfigNotFoundException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        if (null === $config->getOriginOffice()) {
            return $this->feedbackResponseFactory->createError('bitbag.shopware_poczta_polska_app.config.origin_offices.empty');
        }

        $packageRepository = RepositoryFactory::create('custom_entity_bitbag_shopware_poczta_polska_app_packages');
        $packageCriteria = (new Criteria())->addFilter(new EqualsFilter('order.id', $orderId));
        $package = $packageRepository->searchIds($packageCriteria, $context);
        if (0 !== $package->getTotal()) {
            return $this->feedbackResponseFactory->createError('bitbag.shopware_poczta_polska_app.package.already_created');
        }

        $client = $this->apiResolver->getClient($shopId, $order->salesChannelId);

        try {
            $package = $this->packageApiService->createPackage(
                $shopId,
                $order,
                $context,
                $client
            );
        } catch (StreetCannotBeSplitException | OrderAddressException | PackageException | ConfigNotFoundException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        try {
            $this->documentApiService->addLabelToOrderDocument(
                $package->getGuid(),
                $orderId,
                $order->orderNumber,
                $client,
                $context
            );
        } catch (LabelException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        try {
            $this->packageApiService->sendPackage(
                $package->getGuid(),
                $config->getOriginOffice(),
                $client
            );
        } catch (PackageException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        $this->saveDataToEntity(
            $package->getGuid(),
            $package->getShippingNumber(),
            $orderId,
            $context
        );

        $this->addTrackingCodeToOrderDelivery(
            $order->deliveries?->first(),
            $package->getShippingNumber(),
            $context
        );

        return $this->feedbackResponseFactory->createSuccess('bitbag.shopware_poczta_polska_app.package.created');
    }

    private function saveDataToEntity(
        string $guid,
        string $trackingCode,
        string $orderId,
        Context $context
    ): void {
        $packageRepository = RepositoryFactory::create('custom-entity-bitbag-shopware-poczta-polska-app-packages');

        $packageRepository->create([
            'guid' => $guid,
            'orderNumber' => $trackingCode,
            'orderId' => $orderId,
        ], $context);
    }

    private function addTrackingCodeToOrderDelivery(
        ?OrderDeliveryEntity $orderDelivery,
        string $trackingCode,
        Context $context
    ): void {
        $trackingCodes = $orderDelivery->trackingCodes ?? [];

        if (null !== $orderDelivery && !in_array($trackingCode, $trackingCodes)) {
            $this->orderDeliveryRepository->update([
                'id' => $orderDelivery->id,
                'trackingCodes' => array_merge($trackingCodes, [$trackingCode]),
            ], $context);
        }
    }
}
