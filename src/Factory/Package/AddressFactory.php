<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\ShopwarePocztaPolskaApp\Service\StreetSplitterInterface;
use Vin\ShopwareSdk\Data\Entity\OrderAddress\OrderAddressEntity;

final class AddressFactory implements AddressFactoryInterface
{
    public function __construct(private StreetSplitterInterface $streetSplitter)
    {
        $this->streetSplitter = $streetSplitter;
    }

    public function create(OrderAddressEntity $orderAddress, string $email): Address
    {
        $addressStreet = str_replace(['  ', ' / '], ['', '/'], $orderAddress->street);

        $flatNumber = null;
        [, $street, $houseNumber] = $this->streetSplitter->splitStreet($addressStreet);

        if (false !== strpos($houseNumber, '/')) {
            $explodedHouseNumber = explode('/', $houseNumber);

            [$houseNumber, $flatNumber] = $explodedHouseNumber;
        }

        $address = new Address();
        $address->setName($orderAddress->firstName . ' ' . $orderAddress->lastName);
        $address->setEmail($email);
        $address->setCity($orderAddress->city);
        $address->setPostCode($orderAddress->zipcode);
        $address->setStreet($street);
        if (null !== $flatNumber) {
            $address->setFlatNumber(trim($flatNumber));
        }
        $address->setHouseNumber(trim($houseNumber));
        $address->setMobileNumber($orderAddress->phoneNumber);

        return $address;
    }
}
