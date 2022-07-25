<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Validator;

use BitBag\ShopwarePocztaPolskaApp\Validator\PhoneNumberValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PhoneNumberValidatorTest extends TestCase
{
    public function testValidateCorrectPhoneNumber(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')
                   ->willReturn('foo');
        $validator = new PhoneNumberValidator($translator);
        self::assertEquals(
            0,
            $validator->validate('500-000-000')->count()
        );
    }

    public function testValidateIncorrectPhoneNumber(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')
                   ->willReturn('foo');
        $validator = new PhoneNumberValidator($translator);
        self::assertEquals(
            1,
            $validator->validate('12345678900')->count()
        );
    }
}
