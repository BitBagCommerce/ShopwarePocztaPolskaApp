<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\ShopwarePocztaPolskaApp\Factory\Package\AddressFactory;
use BitBag\ShopwarePocztaPolskaApp\Service\StreetSplitterInterface;
use BitBag\ShopwarePocztaPolskaApp\Validator\PhoneNumberValidatorInterface;
use BitBag\ShopwarePocztaPolskaApp\Validator\PostalCodeValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Vin\ShopwareSdk\Data\Entity\OrderAddress\OrderAddressEntity;

final class AddressFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $streetSplitter = $this->createMock(StreetSplitterInterface::class);
        $streetSplitter
            ->method('splitStreet')
            ->willReturn(['Jasna 4/5', 'Jasna', '4/5']);
        $phoneNumberValidator = $this->createMock(PhoneNumberValidatorInterface::class);
        $phoneNumberValidator->method('validate')->willReturn(new ConstraintViolationList());
        $postalCodeValidator = $this->createMock(PostalCodeValidatorInterface::class);
        $postalCodeValidator->method('validate')->willReturn(new ConstraintViolationList());

        $orderAddressEntity = new OrderAddressEntity();
        $orderAddressEntity->firstName = 'Jan';
        $orderAddressEntity->lastName = 'Kowalski';
        $orderAddressEntity->street = 'Jasna 4/5';
        $orderAddressEntity->city = 'Warszawa';
        $orderAddressEntity->zipcode = '02-495';
        $orderAddressEntity->phoneNumber = '500-000-000';

        $address = new Address();
        $address->setName('Jan Kowalski');
        $address->setStreet('Jasna');
        $address->setHouseNumber('4');
        $address->setFlatNumber('5');
        $address->setCity('Warszawa');
        $address->setPostCode('02-495');
        $address->setMobileNumber('500-000-000');
        $address->setEmail('email@test.com');

        $addressFactory = new AddressFactory(
            $streetSplitter,
            $phoneNumberValidator,
            $postalCodeValidator
        );

        $this->assertEquals(
            $address,
            $addressFactory->create($orderAddressEntity, 'email@test.com')
        );
    }
}
