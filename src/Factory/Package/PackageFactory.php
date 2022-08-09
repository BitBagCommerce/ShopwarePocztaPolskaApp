<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\PPClient\Model\COD;
use BitBag\PPClient\Model\EpoSimple;
use BitBag\PPClient\Model\PackageContent;
use BitBag\PPClient\Model\PaidByEnum;
use BitBag\PPClient\Model\PaidByReceiver;
use BitBag\PPClient\Model\PaidByReceiverEnum;
use BitBag\PPClient\Model\PocztexCourier;
use BitBag\PPClient\Model\RecordedDelivery;
use BitBag\ShopwarePocztaPolskaApp\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwarePocztaPolskaApp\Guid\Guid;
use BitBag\ShopwarePocztaPolskaApp\Resolver\OrderCustomFieldResolverInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\PackageSizeResolverInterface;
use DateTime;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\Entity\PaymentMethod\PaymentMethodEntity;

final class PackageFactory implements PackageFactoryInterface
{
    public function __construct(
        private OrderWeightCalculatorInterface $orderWeightCalculator,
        private OrderCustomFieldResolverInterface $orderCustomFieldResolver,
        private PackageSizeResolverInterface $packageSizeResolver
    ) {
        $this->orderWeightCalculator = $orderWeightCalculator;
        $this->orderCustomFieldResolver = $orderCustomFieldResolver;
        $this->packageSizeResolver = $packageSizeResolver;
    }

    public function create(
        OrderEntity $order,
        Address $address,
        Context $context
    ): RecordedDelivery {
        $customFields = $this->orderCustomFieldResolver->resolve($order);
        $guid = Guid::generate();
        $weight = $this->orderWeightCalculator->calculate($order, $context);
        $packageSize = $this->packageSizeResolver->resolve(
            $customFields->getDepth(),
            $customFields->getHeight(),
            $customFields->getWidth(),
            (int) round($weight)
        );
        $description = $customFields->getPackageContents();
        $totalAmount = (int) ($order->amountTotal * 100);

        $package = new PocztexCourier();
        $package->setGuid($guid);
        $package->setAddress($address);
        $package->setPlannedShippingDate(new DateTime($customFields->getPlannedShippingDate()));
        $package->setWeight((int) ($weight * 1000));
        $package->setTotalAmount($totalAmount);
        $package->setPacketGuid($guid);
        $package->setPackagingGuid($guid);
        $package->setDescription($description);
        $package->setEpo(new EpoSimple());
        $package->setPaidBy(PaidByEnum::SENDER);
        $package->setPocztexPackageFormat($packageSize);

        $packageContent = new PackageContent();
        $packageContent->setAnotherPackageContent($description);

        $package->setPackageContents($packageContent);

        $paymentMethod = $order->transactions?->first()?->paymentMethod;
        if ($this->isCashOnDelivery($paymentMethod)) {
            $package->setPaidBy(PaidByEnum::ADDRESSEE);

            $paidByReceiver = new PaidByReceiver();
            $paidByReceiver->setType(PaidByReceiverEnum::INDIVIDUAL_RECEIVER);

            $package->setPaidByReceiver($paidByReceiver);

            $cod = new COD();
            $cod->setTotalAmount($totalAmount);
            $cod->setCodType(COD::COD_TYPE_POSTAL_ORDER);
            $cod->setToBeCheckedByReceiver(false);

            $package->setCod($cod);
        }

        return $package;
    }

    private function isCashOnDelivery(?PaymentMethodEntity $paymentMethod): bool
    {
        if (null === $paymentMethod) {
            return false;
        }

        $orderPaymentMethodHandlerIdentifier = $paymentMethod->handlerIdentifier;

        return self::CASH_PAYMENT_CLASS === $orderPaymentMethodHandlerIdentifier;
    }
}
