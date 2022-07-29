<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\PPClient\Model\COD;
use BitBag\PPClient\Model\CODShipment;
use BitBag\PPClient\Model\PostalPackage;
use BitBag\PPClient\Model\RecordedDelivery;
use BitBag\ShopwarePocztaPolskaApp\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwarePocztaPolskaApp\Provider\Guid;
use BitBag\ShopwarePocztaPolskaApp\Resolver\OrderCustomFieldResolverInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\PackageSizeResolverInterface;
use DateTime;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\Entity\PaymentMethod\PaymentMethodEntity;

final class PostalPackageFactory implements PostalPackageFactoryInterface
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
        $packageSize = $this->packageSizeResolver->resolve(
            $customFields->getDepth(),
            $customFields->getHeight(),
            $customFields->getWidth()
        );
        $plannedShippingDate = $customFields->getPlannedShippingDate();
        $description = $customFields->getPackageContents();
        $weight = $this->orderWeightCalculator->calculate($order, $context);
        $totalAmount = (int) ($order->amountTotal * 100);

        $paymentMethod = $order->transactions?->first()?->paymentMethod;

        $package = $this->isOrderCashOnDelivery($paymentMethod) ? new CODShipment() : new PostalPackage();
        $package->setGuid($guid);
        $package->setAddress($address);
        $package->setCategory(RecordedDelivery::CATEGORY_PRIORITY);
        $package->setPackageSize($packageSize);
        $package->setPlannedShippingDate(new DateTime($plannedShippingDate));
        $package->setWeight((int) round($weight));
        $package->setTotalAmount($totalAmount);
        $package->setPacketGuid($guid);
        $package->setPackagingGuid($guid);
        $package->setDescription($description);

        if ($this->isOrderCashOnDelivery($paymentMethod) && $package instanceof CODShipment) {
            $cod = new COD();
            $cod->setTotalAmount($totalAmount);
            $cod->setCodType(COD::COD_TYPE_POSTAL_ORDER);
            $cod->setToBeCheckedByReceiver(false);

            $package->setCod($cod);
        }

        return $package;
    }

    private function isOrderCashOnDelivery(?PaymentMethodEntity $paymentMethod): bool
    {
        if (null === $paymentMethod) {
            return false;
        }

        $orderPaymentMethodHandlerIdentifier = $paymentMethod->handlerIdentifier;

        return self::CASH_PAYMENT_CLASS === $orderPaymentMethodHandlerIdentifier;
    }
}
