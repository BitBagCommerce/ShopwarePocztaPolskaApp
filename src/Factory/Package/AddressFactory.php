<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderAddressException;
use BitBag\ShopwarePocztaPolskaApp\Service\StreetSplitterInterface;
use BitBag\ShopwarePocztaPolskaApp\Validator\IsPhoneNumber;
use BitBag\ShopwarePocztaPolskaApp\Validator\IsPostalCode;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Vin\ShopwareSdk\Data\Entity\OrderAddress\OrderAddressEntity;

final class AddressFactory implements AddressFactoryInterface
{
    public function __construct(
        private StreetSplitterInterface $streetSplitter,
        private ValidatorInterface $validator
    ) {
    }

    public function create(OrderAddressEntity $orderAddress, string $email): Address
    {
        $addressStreet = str_replace(['  ', ' / '], ['', '/'], $orderAddress->street);

        $flatNumber = null;
        [, $street, $houseNumber] = $this->streetSplitter->splitStreet($addressStreet);

        if (str_contains($houseNumber, '/')) {
            $explodedHouseNumber = explode('/', $houseNumber);

            [$houseNumber, $flatNumber] = $explodedHouseNumber;
        }

        $phoneNumber = $orderAddress->phoneNumber;
        $phoneNumberValidator = $this->validator->validate($phoneNumber, new IsPhoneNumber());
        if (0 !== $phoneNumberValidator->count()) {
            throw new OrderAddressException((string) $phoneNumberValidator->get(0)->getMessage());
        }

        $postalCode = $orderAddress->zipcode;
        $postalCodeValidator = $this->validator->validate($postalCode, new IsPostalCode());
        if (0 !== $postalCodeValidator->count()) {
            throw new OrderAddressException((string) $postalCodeValidator->get(0)->getMessage());
        }

        $address = new Address();
        $address->setName($orderAddress->firstName . ' ' . $orderAddress->lastName);
        $address->setEmail($email);
        $address->setCity($orderAddress->city);
        $address->setPostCode($postalCode);
        $address->setStreet($street);
        $address->setFlatNumber(trim($flatNumber ?? ''));
        $address->setHouseNumber(trim($houseNumber));
        $address->setMobileNumber($phoneNumber);

        return $address;
    }
}
