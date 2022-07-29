<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Controller;

use BitBag\ShopwareAppSystemBundle\Model\Action\ActionInterface;
use BitBag\ShopwarePocztaPolskaApp\Api\OrderDeliveryApiServiceInterface;
use BitBag\ShopwarePocztaPolskaApp\Api\PackageApiServiceInterface;
use BitBag\ShopwarePocztaPolskaApp\Exception\ConfigException;
use BitBag\ShopwarePocztaPolskaApp\Exception\ConfigNotFoundException;
use BitBag\ShopwarePocztaPolskaApp\Factory\FeedbackResponseFactoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\ApiResolverInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\ConfigResolverInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\OrderResolverInterface;
use Symfony\Component\HttpFoundation\Response;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Factory\RepositoryFactory;

final class CreatePackageController
{
    public const SHIPPING_KEY = 'Poczta Polska';

    public function __construct(
        private FeedbackResponseFactoryInterface $feedbackResponseFactory,
        private ApiResolverInterface $apiResolver,
        private PackageApiServiceInterface $packageApiService,
        private OrderDeliveryApiServiceInterface $orderDeliveryApiService,
        private OrderResolverInterface $orderResolver,
        private ConfigResolverInterface $configResolver
    ) {
    }

    public function create(ActionInterface $action, Context $context): Response
    {
        $orderId = $action->getData()->getIds()[0] ?? '';
        $shopId = $action->getSource()->getShopId();

        try {
            $order = $this->orderResolver->getOrder($orderId, $shopId, $context);
        } catch (\Exception $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        try {
            $config = $this->configResolver->getConfig($shopId, $order->salesChannelId);
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
                $config->getOriginOffice(),
                $order,
                $context,
                $client
            );
        } catch (\Exception $e) {
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
