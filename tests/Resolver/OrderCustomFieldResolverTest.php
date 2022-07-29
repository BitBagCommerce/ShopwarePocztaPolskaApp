<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Resolver;

use BitBag\ShopwarePocztaPolskaApp\Model\OrderCustomFieldModel;
use BitBag\ShopwarePocztaPolskaApp\Resolver\OrderCustomFieldResolver;
use BitBag\ShopwarePocztaPolskaApp\Resolver\OrderCustomFieldResolverInterface;
use BitBag\ShopwarePocztaPolskaApp\Validator\OrderCustomFieldValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class OrderCustomFieldResolverTest extends TestCase
{
    public function testResolveWithCustomFields(): void
    {
        $orderCustomFieldValidator = $this->createMock(OrderCustomFieldValidatorInterface::class);
        $orderCustomFieldValidator->method('validate')
                                  ->willReturn(new ConstraintViolationList());
        $orderCustomFieldResolver = new OrderCustomFieldResolver($orderCustomFieldValidator);
        $order = $this->getOrderWithCustomFields();
        $orderCustomFieldModel = new OrderCustomFieldModel(
            'depth_foo',
            'height_foo',
            'width_foo',
            'package_contents_foo',
            'planned_shipping_date_foo'
        );

        self::assertEquals(
            $orderCustomFieldModel,
            $orderCustomFieldResolver->resolve($order)
        );
    }

    public function testExpectedError(): void
    {
        $this->expectExceptionMessage('bitbag.shopware_poczta_polska_app.order.custom_fields.depth_invalid');

        $validator = Validation::createValidatorBuilder()->getValidator();

        $constraint = new Length(
            null,
            1,
            null,
            null,
            null,
            null,
            'bitbag.shopware_poczta_polska_app.order.custom_fields.depth_invalid'
        );

        $orderCustomFieldValidator = $this->createMock(OrderCustomFieldValidatorInterface::class);
        $orderCustomFieldValidator->method('validate')
                                  ->willReturn($validator->validate('', $constraint));

        $order = new OrderEntity();
        $order->setCustomFields([]);

        $orderCustomFieldResolver = new OrderCustomFieldResolver($orderCustomFieldValidator);
        $orderCustomFieldResolver->resolve($order);
    }

    private function getOrderWithCustomFields(): OrderEntity
    {
        $order = new OrderEntity();

        $customFields = [
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_depth' => 'depth_foo',
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_height' => 'height_foo',
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_width' => 'width_foo',
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_package_contents' => 'package_contents_foo',
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_planned_shipping_date' => 'planned_shipping_date_foo',
        ];

        $order->setCustomFields($customFields);

        return $order;
    }
}
