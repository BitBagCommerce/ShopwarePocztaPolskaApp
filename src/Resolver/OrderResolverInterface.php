<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Resolver;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface OrderResolverInterface
{
    public function getOrder(
        string $orderId,
        string $shopId,
        Context $context
    ): OrderEntity;
}
