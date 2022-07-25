<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Factory\Package;

use BitBag\PPClient\Model\Address;
use Vin\ShopwareSdk\Data\Entity\OrderAddress\OrderAddressEntity;

interface AddressFactoryInterface
{
    public function create(OrderAddressEntity $orderAddress, string $email): Address;
}
