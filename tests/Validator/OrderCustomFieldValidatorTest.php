<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Validator;

use BitBag\ShopwarePocztaPolskaApp\Resolver\OrderCustomFieldResolverInterface;
use BitBag\ShopwarePocztaPolskaApp\Validator\OrderCustomFieldValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderCustomFieldValidatorTest extends TestCase
{
    private array $data;

    protected function setUp(): void
    {
        $this->data = [
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_depth' => 'foo',
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_height' => 'foo',
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_width' => 'foo',
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_package_contents' => 'foo',
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_planned_shipping_date' => 'foo',
        ];
    }

    public function testValidateEmptyDepth(): void
    {
        $data = $this->data;
        unset($data['bitbag_shopware_poczta_polska_app_package_details_depth']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')
                   ->willReturn('foo');
        $orderCustomFieldValidator = new OrderCustomFieldValidator($translator);
        self::assertEquals(
            1,
            $orderCustomFieldValidator->validate($data)->count()
        );
    }

    public function testValidateEmptyHeight(): void
    {
        $data = $this->data;
        unset($data['bitbag_shopware_poczta_polska_app_package_details_height']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')
                   ->willReturn('foo');
        $orderCustomFieldValidator = new OrderCustomFieldValidator($translator);
        self::assertEquals(
            1,
            $orderCustomFieldValidator->validate($data)->count()
        );
    }

    public function testValidateEmptyWidth(): void
    {
        $data = $this->data;
        unset($data['bitbag_shopware_poczta_polska_app_package_details_width']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')
                   ->willReturn('foo');
        $orderCustomFieldValidator = new OrderCustomFieldValidator($translator);
        self::assertEquals(
            1,
            $orderCustomFieldValidator->validate($data)->count()
        );
    }

    public function testValidateEmptyPackageContents(): void
    {
        $data = $this->data;
        unset($data['bitbag_shopware_poczta_polska_app_package_details_package_contents']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')
                   ->willReturn('foo');
        $orderCustomFieldValidator = new OrderCustomFieldValidator($translator);
        self::assertEquals(
            1,
            $orderCustomFieldValidator->validate($data)->count()
        );
    }

    public function testValidateEmptyPlannedShippingDate(): void
    {
        $data = $this->data;
        unset($data['bitbag_shopware_poczta_polska_app_package_details_planned_shipping_date']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')
                   ->willReturn('foo');
        $orderCustomFieldValidator = new OrderCustomFieldValidator($translator);
        self::assertEquals(
            1,
            $orderCustomFieldValidator->validate($data)->count()
        );
    }
}
