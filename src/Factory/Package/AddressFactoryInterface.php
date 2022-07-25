<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Factory\Package;

use BitBag\PPClient\Model\Address;
use Vin\ShopwareSdk\Data\Entity\OrderAddress\OrderAddressEntity;

interface AddressFactoryInterface
{
    public const PHONE_NUMBER_REGEX = "/(?:(?:\+|00)[0-9]{1,3})?(\d{9,12})/";

    public const PHONE_NUMBER_LENGTH = 9;

    public const POST_CODE_REGEX = "/^(\d{2})(-\d{3})?$/i";

    public function create(OrderAddressEntity $orderAddress, string $email): Address;
}
