<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Controller;

use BitBag\ShopwareAppSystemBundle\Model\Action\ActionInterface;
use BitBag\ShopwarePocztaPolskaApp\Api\DocumentApiServiceInterface;
use BitBag\ShopwarePocztaPolskaApp\Api\OrderDeliveryApiServiceInterface;
use BitBag\ShopwarePocztaPolskaApp\Api\PackageApiServiceInterface;
use BitBag\ShopwarePocztaPolskaApp\Entity\ConfigInterface;
use BitBag\ShopwarePocztaPolskaApp\Exception\ConfigException;
use BitBag\ShopwarePocztaPolskaApp\Exception\ConfigNotFoundException;
use BitBag\ShopwarePocztaPolskaApp\Exception\LabelException;
use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderAddressException;
use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderException;
use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderShippingMethodException;
use BitBag\ShopwarePocztaPolskaApp\Exception\PackageException;
use BitBag\ShopwarePocztaPolskaApp\Exception\StreetCannotBeSplitException;
use BitBag\ShopwarePocztaPolskaApp\Factory\FeedbackResponseFactoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Finder\OrderFinderInterface;
use BitBag\ShopwarePocztaPolskaApp\Repository\ConfigRepositoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\ApiResolverInterface;
use BitBag\ShopwarePocztaPolskaApp\Validator\ConfigValidatorInterface;
use BitBag\ShopwarePocztaPolskaApp\Validator\OrderValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Factory\RepositoryFactory;

final class CreatePackageController
{
    public const SHIPPING_KEY = 'Poczta Polska';

    public function __construct(
        private OrderFinderInterface $orderFinder,
        private FeedbackResponseFactoryInterface $feedbackResponseFactory,
        private ApiResolverInterface $apiResolver,
        private PackageApiServiceInterface $packageApiService,
        private ConfigRepositoryInterface $configRepository,
        private DocumentApiServiceInterface $documentApiService,
        private OrderValidatorInterface $orderValidator,
        private ConfigValidatorInterface $configValidator,
        private OrderDeliveryApiServiceInterface $orderDeliveryApiService
    ) {
    }

    public function create(ActionInterface $action, Context $context): Response
    {
        $orderId = $action->getData()->getIds()[0] ?? null;
        $shopId = $action->getSource()->getShopId();

        $order = $this->orderFinder->getWithAssociations($orderId, $context);

        try {
            $this->orderValidator->validate(
                $shopId,
                $order,
                $context
            );
        } catch (OrderShippingMethodException | ConfigException | ConfigNotFoundException | OrderException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        /** @var ConfigInterface $config */
        $config = $this->configRepository->findByShopIdAndSalesChannelId($shopId, $order->salesChannelId);

        try {
            $this->configValidator->validate($config);
        } catch (ConfigException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        try {
            $client = $this->apiResolver->getClient($shopId, $order->salesChannelId);
        } catch (ConfigNotFoundException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        try {
            $package = $this->packageApiService->createPackage(
                $shopId,
                $order,
                $context,
                $client
            );
        } catch (StreetCannotBeSplitException | OrderAddressException | PackageException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        try {
            $this->documentApiService->uploadOrderLabel(
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

        $this->saveDataToCustomEntity(
            $package->getGuid(),
            $package->getShippingNumber(),
            $orderId,
            $context
        );

        $this->orderDeliveryApiService->addTrackingCode(
            $package->getShippingNumber(),
            $order->deliveries?->first(),
            $context
        );

        return $this->feedbackResponseFactory->createSuccess('bitbag.shopware_poczta_polska_app.package.created');
    }

    private function saveDataToCustomEntity(
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
}
