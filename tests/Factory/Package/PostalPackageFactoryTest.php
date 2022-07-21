<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\PPClient\Model\PostalPackage;
use BitBag\PPClient\Model\RecordedDelivery;
use BitBag\ShopwarePocztaPolskaApp\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwarePocztaPolskaApp\Factory\Package\PostalPackageFactory;
use BitBag\ShopwarePocztaPolskaApp\Model\OrderCustomFieldModel;
use BitBag\ShopwarePocztaPolskaApp\Resolver\OrderCustomFieldResolverInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\PackageSizeResolverInterface;
use PHPUnit\Framework\TestCase;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class PostalPackageFactoryTest extends TestCase
{
    private const ORDER_WEIGHT = 5.5;

    private const ROUNDED_WEIGHT = 6;

    private const PLANNED_SHIPPING_DATE = '2022-08-30';

    private const PACKAGE_CONTENTS = 'T-shirt';

    private const TOTAL_AMOUNT = 10.0;

    private const TOTAL_AMOUNT_INT = 1000;

    private const DEPTH = 10;

    private const HEIGHT = 10;

    private const WIDTH = 10;

    public function testCreate(): void
    {
        $order = new OrderEntity();
        $order->amountTotal = self::TOTAL_AMOUNT;
        $order->setCustomFields($this->getCustomFields());

        $address = new Address();
        $context = $this->createMock(Context::class);

        $orderWeightCalculator = $this->createMock(OrderWeightCalculatorInterface::class);
        $orderCustomFieldResolver = $this->createMock(OrderCustomFieldResolverInterface::class);
        $packageSizeResolver = $this->createMock(PackageSizeResolverInterface::class);

        $orderWeightCalculator->method('calculate')->with($order, $context)->willReturn(self::ORDER_WEIGHT);
        $orderCustomFieldResolver->method('resolve')->with($order)->willReturn($this->getCustomFieldsModel());
        $packageSizeResolver->method('resolve')->willReturn(RecordedDelivery::PACKAGE_SIZE_A);

        $postalPackageFactory = new PostalPackageFactory(
            $orderWeightCalculator,
            $orderCustomFieldResolver,
            $packageSizeResolver
        );

        $postalPackageFactory = $postalPackageFactory->create($order, $address, $context);

        $postalPackage = new PostalPackage();
        $postalPackage->setCategory(RecordedDelivery::CATEGORY_PRIORITY);
        $postalPackage->setWeight(self::ROUNDED_WEIGHT);
        $postalPackage->setTotalAmount(self::TOTAL_AMOUNT_INT);
        $postalPackage->setAddress($address);
        $postalPackage->setPlannedShippingDate(new \DateTime(self::PLANNED_SHIPPING_DATE));
        $postalPackage->setDescription(self::PACKAGE_CONTENTS);
        $postalPackage->setPackageSize(RecordedDelivery::PACKAGE_SIZE_A);
        $postalPackage->setGuid($postalPackageFactory->getGuid());
        $postalPackage->setPacketGuid($postalPackageFactory->getPacketGuid());
        $postalPackage->setPackagingGuid($postalPackageFactory->getPackagingGuid());

        self::assertEquals(
            $postalPackage,
            $postalPackageFactory
        );
    }

    private function getCustomFields(): array
    {
        return [
            'depth' => self::DEPTH,
            'height' => self::HEIGHT,
            'width' => self::WIDTH,
            'packageContents' => self::PACKAGE_CONTENTS,
            'plannedShippingDate' => self::PLANNED_SHIPPING_DATE,
        ];
    }

    public function getCustomFieldsModel(): OrderCustomFieldModel
    {
        return new OrderCustomFieldModel(
            self::DEPTH,
            self::HEIGHT,
            self::WIDTH,
            self::PACKAGE_CONTENTS,
            self::PLANNED_SHIPPING_DATE
        );
    }
}
