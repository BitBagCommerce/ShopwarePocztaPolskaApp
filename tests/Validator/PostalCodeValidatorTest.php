<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Validator;

use BitBag\ShopwarePocztaPolskaApp\Validator\PostalCodeValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PostalCodeValidatorTest extends TestCase
{
    public function testValidateCorrectPostalCode(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')
                   ->willReturn('foo');
        $validator = new PostalCodeValidator($translator);
        self::assertEquals(
            0,
            $validator->validate('02-495')->count()
        );
    }

    public function testValidateIncorrectPostalCode(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')
                   ->willReturn('foo');
        $validator = new PostalCodeValidator($translator);
        self::assertEquals(
            1,
            $validator->validate('002495')->count()
        );
    }
}
