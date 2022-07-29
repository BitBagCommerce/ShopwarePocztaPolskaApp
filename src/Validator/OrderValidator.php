<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Validator;

use BitBag\ShopwarePocztaPolskaApp\Controller\CreatePackageController;
use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderException;
use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderShippingMethodException;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\Filter\EqualsFilter;
use Vin\ShopwareSdk\Factory\RepositoryFactory;

final class OrderValidator implements OrderValidatorInterface
{
    public function validate(
        string $shopId,
        OrderEntity $order,
        Context $context
    ): void {
        $shippingMethod = $order->deliveries?->first()?->shippingMethod ?? null;
        if (null === $shippingMethod) {
            throw new OrderShippingMethodException('bitbag.shopware_poczta_polska_app.order.shipping_method.not_found');
        }

        $technicalName = $shippingMethod->getTranslated()['customFields']['technical_name'] ?? null;
        if (CreatePackageController::SHIPPING_KEY !== $technicalName) {
            throw new OrderException('bitbag.shopware_poczta_polska_app.order.shipping_method.not_polish_post');
        }

        $packageRepository = RepositoryFactory::create('custom_entity_bitbag_shopware_poczta_polska_app_packages');
        $packageCriteria = (new Criteria())->addFilter(new EqualsFilter('order.id', $order->id));
        $package = $packageRepository->searchIds($packageCriteria, $context);
        if (0 !== $package->getTotal()) {
            throw new OrderException('bitbag.shopware_poczta_polska_app.package.already_created');
        }
    }
}
