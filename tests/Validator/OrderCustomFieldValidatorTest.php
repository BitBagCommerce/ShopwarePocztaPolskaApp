<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Validator;

use BitBag\ShopwarePocztaPolskaApp\Resolver\OrderCustomFieldResolverInterface;
use BitBag\ShopwarePocztaPolskaApp\Validator\OrderCustomFieldValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderCustomFieldValidatorTest extends TestCase
{
    /** @dataProvider provideData */
    public function testValidator(string $key, string $value): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')
                   ->willReturn('foo');
        $orderCustomFieldValidator = new OrderCustomFieldValidator($translator);
        self::assertEquals(
            4,
            $orderCustomFieldValidator->validate([$key => $value])->count()
        );
    }

    public function provideData(): array
    {
        return [
           [OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_depth', 'foo'],
           [OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_height', 'foo'],
           [OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_width', 'foo'],
           [OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_package_contents', 'foo'],
           [OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_planned_shipping_date', 'foo'],
        ];
    }
}
