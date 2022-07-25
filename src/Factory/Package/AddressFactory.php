<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderAddressException;
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

        if (str_contains($houseNumber, '/')) {
            $explodedHouseNumber = explode('/', $houseNumber);

            [$houseNumber, $flatNumber] = $explodedHouseNumber;
        }

        $phoneNumber = $orderAddress->phoneNumber;
        if (null === $phoneNumber) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.phone_number_empty');
        }

        $phoneNumber = str_replace(['+48', '+', '-', ' '], '', $phoneNumber);
        $this->checkPhoneNumberValidity($phoneNumber);

        $postalCode = str_replace('-', '', $orderAddress->zipcode);
        $this->checkPostCodeValidity($postalCode);

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

    private function checkPhoneNumberValidity(string $phoneNumber): void
    {
        preg_match(self::PHONE_NUMBER_REGEX, $phoneNumber, $phoneNumberMatches);

        if ([] === $phoneNumberMatches) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.phone_number_invalid');
        }

        $phoneNumberLength = strlen($phoneNumberMatches[0]);

        if (self::PHONE_NUMBER_LENGTH !== $phoneNumberLength) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.phone_number_invalid');
        }
    }

    private function isPostCodeValid(string $postCode): bool
    {
        return (bool) preg_match(self::POST_CODE_REGEX, $postCode);
    }

    private function checkPostCodeValidity(string $postCode): void
    {
        if (!$this->isPostCodeValid($postCode)) {
            $postCode = trim(substr_replace($postCode, '-', 2, 0));

            if (!$this->isPostCodeValid($postCode)) {
                throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.post_code_invalid');
            }
        }
    }
}
