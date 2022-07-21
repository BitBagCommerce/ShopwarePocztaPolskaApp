<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Validator;

use BitBag\ShopwarePocztaPolskaApp\Resolver\OrderCustomFieldResolverInterface;
use BitBag\ShopwarePocztaPolskaApp\Validator\OrderCustomFieldValidator;
use PHPUnit\Framework\TestCase;

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
        $this->expectExceptionMessage('bitbag.shopware_poczta_polska_app.order.custom_fields.depth_invalid');

        $data = $this->data;
        unset($data['bitbag_shopware_poczta_polska_app_package_details_depth']);

        $orderCustomFieldValidator = new OrderCustomFieldValidator();
        $orderCustomFieldValidator->validate($data);
    }

    public function testValidateEmptyHeight(): void
    {
        $this->expectExceptionMessage('bitbag.shopware_poczta_polska_app.order.custom_fields.height_invalid');

        $data = $this->data;
        unset($data['bitbag_shopware_poczta_polska_app_package_details_height']);

        $orderCustomFieldValidator = new OrderCustomFieldValidator();
        $orderCustomFieldValidator->validate($data);
    }

    public function testValidateEmptyWidth(): void
    {
        $this->expectExceptionMessage('bitbag.shopware_poczta_polska_app.order.custom_fields.width_invalid');

        $data = $this->data;
        unset($data['bitbag_shopware_poczta_polska_app_package_details_width']);

        $orderCustomFieldValidator = new OrderCustomFieldValidator();
        $orderCustomFieldValidator->validate($data);
    }

    public function testValidateEmptyPackageContents(): void
    {
        $this->expectExceptionMessage('bitbag.shopware_poczta_polska_app.order.custom_fields.package_contents_invalid');

        $data = $this->data;
        unset($data['bitbag_shopware_poczta_polska_app_package_details_package_contents']);

        $orderCustomFieldValidator = new OrderCustomFieldValidator();
        $orderCustomFieldValidator->validate($data);
    }

    public function testValidateEmptyPlannedShippingDate(): void
    {
        $this->expectExceptionMessage('bitbag.shopware_poczta_polska_app.order.custom_fields.planned_shipping_date_invalid');

        $data = $this->data;
        unset($data['bitbag_shopware_poczta_polska_app_package_details_planned_shipping_date']);

        $orderCustomFieldValidator = new OrderCustomFieldValidator();
        $orderCustomFieldValidator->validate($data);
    }
}
