<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\PPClient\Model\PostalPackage;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface PostalPackageFactoryInterface
{
    public function create(
        OrderEntity $order,
        Address $address,
        Context $context
    ): PostalPackage;
}