<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Resolver;

use BitBag\ShopwarePocztaPolskaApp\Validator\OrderCustomFieldValidator;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class OrderCustomFieldResolver implements OrderCustomFieldResolverInterface
{
    public function __construct(private OrderCustomFieldValidator $orderCustomFieldValidator)
    {
        $this->orderCustomFieldValidator = $orderCustomFieldValidator;
    }

    public function resolve(OrderEntity $order): array
    {
        $packageDetailsKey = self::PACKAGE_DETAILS_KEY;
        /**
         * @psalm-var array<array-key, mixed>|null
         */
        $orderCustomFields = $order->getCustomFields() ?? [];
        $this->orderCustomFieldValidator->validate($orderCustomFields);
        $depthKey = $packageDetailsKey . '_depth';
        $heightKey = $packageDetailsKey . '_height';
        $widthKey = $packageDetailsKey . '_width';
        $packageContentsKey = $packageDetailsKey . '_package_contents';
        $plannedShippingDate = $packageDetailsKey . '_planned_shipping_date';

        return [
            'depth' => $orderCustomFields[$depthKey],
            'height' => $orderCustomFields[$heightKey],
            'width' => $orderCustomFields[$widthKey],
            'packageContents' => $orderCustomFields[$packageContentsKey],
            'plannedShippingDate' => $orderCustomFields[$plannedShippingDate],
        ];
    }
}
