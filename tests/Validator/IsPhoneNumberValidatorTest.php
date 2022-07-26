<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Validator;

use BitBag\ShopwarePocztaPolskaApp\Validator\IsPhoneNumber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class IsPhoneNumberValidatorTest extends TestCase
{
    public function testValidateCorrectPhoneNumber(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        self::assertEquals(
            0,
            $validator->validate('500-000-000', new IsPhoneNumber())->count()
        );
    }

    public function testValidateIncorrectPhoneNumber(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $constraintViolation = new ConstraintViolation(
            'bitbag.shopware_poczta_polska_app.order.address.phone_number_invalid',
            'bitbag.shopware_poczta_polska_app.order.address.phone_number_invalid',
            [],
            '12345678900',
            '',
            '12345678900'
        );
        $validator->method('validate')->willReturn(new ConstraintViolationList([$constraintViolation]));

        self::assertEquals(
            1,
            $validator->validate('12345678900')->count()
        );
    }
}
