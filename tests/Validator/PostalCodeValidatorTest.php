<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Validator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PostalCodeValidatorTest extends TestCase
{
    public function testValidateCorrectPostalCode(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        self::assertEquals(
            0,
            $validator->validate('02-495')->count()
        );
    }

    public function testValidateIncorrectPostalCode(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $constraintViolation = new ConstraintViolation(
            'bitbag.shopware_poczta_polska_app.order.address.post_code_invalid',
            'bitbag.shopware_poczta_polska_app.order.address.post_code_invalid',
            [],
            '002495',
            '',
            '002495'
        );
        $validator->method('validate')->willReturn(new ConstraintViolationList([$constraintViolation]));
        self::assertEquals(
            1,
            $validator->validate('002495')->count()
        );
    }
}
