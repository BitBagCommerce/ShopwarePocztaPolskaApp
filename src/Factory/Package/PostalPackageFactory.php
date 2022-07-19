<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\PPClient\Model\PostalPackage;
use BitBag\PPClient\Model\RecordedDelivery;
use BitBag\ShopwarePocztaPolskaApp\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwarePocztaPolskaApp\Exception\PackageException;
use BitBag\ShopwarePocztaPolskaApp\Resolver\OrderCustomFieldsResolverInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\PackageSizeResolverInterface;
use DateTime;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class PostalPackageFactory implements PostalPackageFactoryInterface
{
    public function __construct(
        private OrderWeightCalculatorInterface $orderWeightCalculator,
        private OrderCustomFieldsResolverInterface $orderCustomFieldsResolver,
        private PackageSizeResolverInterface $packageSizeResolver
    ) {
        $this->orderWeightCalculator = $orderWeightCalculator;
        $this->orderCustomFieldsResolver = $orderCustomFieldsResolver;
        $this->packageSizeResolver = $packageSizeResolver;
    }

    public function create(
        OrderEntity $order,
        Address $address,
        Context $context
    ): PostalPackage {
        $customFields = $this->orderCustomFieldsResolver->resolve($order);
        $guid = $this->getGuid();
        $packageSize = $this->packageSizeResolver->resolve(
            $customFields['depth'],
            $customFields['height'],
            $customFields['width']
        );

        $plannedShippingDate = $customFields['plannedShippingDate'];
        $description = $customFields['packageContents'];

        $weight = $this->orderWeightCalculator->calculate($order, $context);

        if (0.0 === $weight) {
            throw new PackageException('bitbag.shopware_poczta_polska_app.package.null_weight');
        }

        if (PackageSizeResolverInterface::MAX_WEIGHT_PACKAGE < $weight) {
            throw new PackageException('bitbag.shopware_poczta_polska_app.package.too_heavy');
        }

        $package = new PostalPackage();
        $package->setGuid($guid);
        $package->setAddress($address);
        $package->setCategory(RecordedDelivery::CATEGORY_PRIORITY);
        $package->setPackageSize($packageSize);
        $package->setPlannedShippingDate(new DateTime($plannedShippingDate));
        $package->setWeight((int) round($weight));
        $package->setTotalAmount((int) (round($order->amountTotal) * 100));
        $package->setPacketGuid($guid);
        $package->setPackagingGuid($guid);
        $package->setDescription($description);

        return $package;
    }

    private function getGuid(): string
    {
        return strtoupper(md5(uniqid((string) random_int(32, 32), true)));
    }
}
