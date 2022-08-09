<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Validator;

use BitBag\ShopwarePocztaPolskaApp\Guid\Guid;
use BitBag\ShopwarePocztaPolskaApp\Validator\OrderValidator;
use PHPUnit\Framework\TestCase;
use Vin\ShopwareSdk\Data\AccessToken;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\Entity\OrderDelivery\OrderDeliveryCollection;
use Vin\ShopwareSdk\Data\Entity\OrderDelivery\OrderDeliveryEntity;
use Vin\ShopwareSdk\Data\Entity\ShippingMethod\ShippingMethodEntity;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class OrderValidatorTest extends TestCase
{
    public function testValidateNullShippingMethod(): void
    {
        $this->expectExceptionMessage('bitbag.shopware_poczta_polska_app.order.shipping_method.not_found');

        $packageRepository = $this->createMock(RepositoryInterface::class);
        $validator = new OrderValidator($packageRepository);
        $context = new Context('http://shopware', new AccessToken('access-token'));

        $order = new OrderEntity();
        $order->id = Guid::generate();

        $validator->validate('1234', $order, $context);
    }

    public function testValidateEmptyTechnicalName(): void
    {
        $this->expectExceptionMessage('bitbag.shopware_poczta_polska_app.order.shipping_method.not_polish_post');

        $packageRepository = $this->createMock(RepositoryInterface::class);
        $validator = new OrderValidator($packageRepository);
        $context = new Context('', new AccessToken('access-token'));

        $shippingMethod = new ShippingMethodEntity();
        $shippingMethod->name = 'shipping-method';

        $orderDelivery = new  OrderDeliveryEntity();
        $orderDelivery->trackingCodes = [];
        $orderDelivery->shippingMethod = $shippingMethod;

        $order = new OrderEntity();
        $order->id = Guid::generate();
        $order->deliveries = new OrderDeliveryCollection([$orderDelivery]);

        $validator->validate('1234', $order, $context);
    }
}
