<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\PPClient\Model\COD;
use BitBag\PPClient\Model\EpoSimple;
use BitBag\PPClient\Model\PackageContent;
use BitBag\PPClient\Model\PaidByEnum;
use BitBag\PPClient\Model\PaidByReceiver;
use BitBag\PPClient\Model\PaidByReceiverEnum;
use BitBag\PPClient\Model\PocztexCourier;
use BitBag\PPClient\Model\PocztexPackageSizeEnum;
use BitBag\ShopwarePocztaPolskaApp\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwarePocztaPolskaApp\Factory\Package\PackageFactory;
use BitBag\ShopwarePocztaPolskaApp\Factory\Package\PackageFactoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Model\OrderCustomFieldModel;
use BitBag\ShopwarePocztaPolskaApp\Resolver\OrderCustomFieldResolverInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\PackageSizeResolverInterface;
use PHPUnit\Framework\TestCase;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\Entity\OrderTransaction\OrderTransactionCollection;
use Vin\ShopwareSdk\Data\Entity\OrderTransaction\OrderTransactionEntity;
use Vin\ShopwareSdk\Data\Entity\PaymentMethod\PaymentMethodEntity;

final class PackageFactoryTest extends TestCase
{
    private const ORDER_WEIGHT = 5.5;

    private const PLANNED_SHIPPING_DATE = '2022-08-30';

    private const PACKAGE_CONTENTS = 'T-shirt';

    private const TOTAL_AMOUNT = 10.0;

    private const TOTAL_AMOUNT_INT = 1000;

    private const DEPTH = 10;

    private const HEIGHT = 10;

    private const WIDTH = 10;

    public function testCreateWithPaymentPaidInAdvance(): void
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
        $packageSizeResolver->method('resolve')->willReturn(PocztexPackageSizeEnum::S);

        $packageFactory = new PackageFactory(
            $orderWeightCalculator,
            $orderCustomFieldResolver,
            $packageSizeResolver
        );

        $packageFactory = $packageFactory->create($order, $address, $context);

        $package = new PocztexCourier();
        $package->setGuid($packageFactory->getGuid());
        $package->setAddress($address);
        $package->setPlannedShippingDate(new \DateTime(self::PLANNED_SHIPPING_DATE));
        $package->setWeight((int) (self::ORDER_WEIGHT * 1000));
        $package->setTotalAmount(self::TOTAL_AMOUNT_INT);
        $package->setPacketGuid($packageFactory->getPacketGuid());
        $package->setPackagingGuid($packageFactory->getPackagingGuid());
        $package->setDescription(self::PACKAGE_CONTENTS);
        $package->setEpo(new EpoSimple());
        $package->setPaidBy(PaidByEnum::SENDER);
        $package->setPocztexPackageFormat(PocztexPackageSizeEnum::S);

        $packageContent = new PackageContent();
        $packageContent->setAnotherPackageContent(self::PACKAGE_CONTENTS);

        $package->setPackageContents($packageContent);

        self::assertEquals(
            $package,
            $packageFactory
        );
    }

    public function testCreateWithPaymentCashOnDelivery(): void
    {
        $order = new OrderEntity();
        $order->amountTotal = self::TOTAL_AMOUNT;
        $order->setCustomFields($this->getCustomFields());

        $paymentMethod = new PaymentMethodEntity();
        $paymentMethod->handlerIdentifier = PackageFactoryInterface::CASH_PAYMENT_CLASS;

        $orderTransaction = new OrderTransactionEntity();
        $orderTransaction->paymentMethod = $paymentMethod;

        $order->transactions = new OrderTransactionCollection([$orderTransaction]);

        $context = $this->createMock(Context::class);

        $orderWeightCalculator = $this->createMock(OrderWeightCalculatorInterface::class);
        $orderWeightCalculator->method('calculate')->with($order, $context)->willReturn(self::ORDER_WEIGHT);

        $orderCustomFieldResolver = $this->createMock(OrderCustomFieldResolverInterface::class);
        $orderCustomFieldResolver->method('resolve')->with($order)->willReturn($this->getCustomFieldsModel());

        $packageSizeResolver = $this->createMock(PackageSizeResolverInterface::class);
        $packageSizeResolver->method('resolve')->willReturn(PocztexPackageSizeEnum::S);

        $packageFactory = new PackageFactory(
            $orderWeightCalculator,
            $orderCustomFieldResolver,
            $packageSizeResolver
        );

        $address = new Address();

        $packageFactory = $packageFactory->create($order, $address, $context);

        $package = new PocztexCourier();
        $package->setGuid($packageFactory->getGuid());
        $package->setAddress($address);
        $package->setPlannedShippingDate(new \DateTime(self::PLANNED_SHIPPING_DATE));
        $package->setWeight((int) (self::ORDER_WEIGHT * 1000));
        $package->setTotalAmount(self::TOTAL_AMOUNT_INT);
        $package->setPacketGuid($packageFactory->getPacketGuid());
        $package->setPackagingGuid($packageFactory->getPackagingGuid());
        $package->setDescription(self::PACKAGE_CONTENTS);
        $package->setEpo(new EpoSimple());
        $package->setPaidBy(PaidByEnum::ADDRESSEE);
        $package->setPocztexPackageFormat(PocztexPackageSizeEnum::S);

        $packageContent = new PackageContent();
        $packageContent->setAnotherPackageContent(self::PACKAGE_CONTENTS);

        $package->setPackageContents($packageContent);

        $paidByReceiver = new PaidByReceiver();
        $paidByReceiver->setType(PaidByReceiverEnum::INDIVIDUAL_RECEIVER);

        $package->setPaidByReceiver($paidByReceiver);

        $cod = new COD();
        $cod->setTotalAmount(self::TOTAL_AMOUNT_INT);
        $cod->setCodType(COD::COD_TYPE_POSTAL_ORDER);
        $cod->setToBeCheckedByReceiver(false);

        $package->setCod($cod);

        self::assertEquals(
            $package,
            $packageFactory
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

    private function getCustomFieldsModel(): OrderCustomFieldModel
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
