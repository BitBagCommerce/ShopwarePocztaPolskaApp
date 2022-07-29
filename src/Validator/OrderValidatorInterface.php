<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Validator;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface OrderValidatorInterface
{
    public function validate(
        string $shopId,
        OrderEntity $order,
        Context $context
    ): void;
}
