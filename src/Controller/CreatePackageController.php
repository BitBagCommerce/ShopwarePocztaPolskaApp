<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

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
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class CreatePackageController
{
    public const SHIPPING_KEY = 'Poczta Polska (Kurier)';

    public function __construct(
        private FeedbackResponseFactoryInterface $feedbackResponseFactory,
        private ApiResolverInterface $apiResolver,
        private PackageApiServiceInterface $packageApiService,
        private OrderDeliveryApiServiceInterface $orderDeliveryApiService,
        private OrderResolverInterface $orderResolver,
        private ConfigResolverInterface $configResolver,
        private RepositoryInterface $packageRepository
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
        $this->packageRepository->create([
            'guid' => $guid,
            'orderNumber' => $trackingCode,
            'orderId' => $orderId,
        ], $context);
    }
}
