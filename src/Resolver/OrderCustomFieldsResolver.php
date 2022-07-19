<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Resolver;

use BitBag\ShopwarePocztaPolskaApp\Exception\PackageException;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class OrderCustomFieldsResolver implements OrderCustomFieldsResolverInterface
{
    public function resolve(OrderEntity $order): array
    {
        $packageDetailsKey = self::PACKAGE_DETAILS_KEY;

        /**
         * @psalm-var array<array-key, mixed>|null
         */
        $orderCustomFields = $order->getCustomFields();

        if (empty($orderCustomFields)) {
            throw new PackageException('3bitbag.shopware_poczta_polska_app.package.fill_required_custom_fields');
        }

        $depthKey = $packageDetailsKey . '_depth';
        $heightKey = $packageDetailsKey . '_height';
        $widthKey = $packageDetailsKey . '_width';
        $packageContentsKey = $packageDetailsKey . '_package_contents';
        $plannedShippingDate = $packageDetailsKey . '_planned_shipping_date';

        if (!isset(
            $orderCustomFields[$depthKey],
            $orderCustomFields[$heightKey],
            $orderCustomFields[$widthKey],
            $orderCustomFields[$packageContentsKey],
            $orderCustomFields[$plannedShippingDate]
        )) {
            throw new PackageException('2bitbag.shopware_poczta_polska_app.package.fill_required_custom_fields');
        }

        if (0 === $orderCustomFields[$depthKey] ||
            0 === $orderCustomFields[$heightKey] ||
            0 === $orderCustomFields[$widthKey] ||
            null === $orderCustomFields[$packageContentsKey] ||
            null === $orderCustomFields[$plannedShippingDate]
        ) {
            throw new PackageException('1bitbag.shopware_poczta_polska_app.package.fill_required_custom_fields');
        }

        return [
            'depth' => $orderCustomFields[$depthKey],
            'height' => $orderCustomFields[$heightKey],
            'width' => $orderCustomFields[$widthKey],
            'packageContents' => $orderCustomFields[$packageContentsKey],
            'plannedShippingDate' => $orderCustomFields[$plannedShippingDate],
        ];
    }
}
