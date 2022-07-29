<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Api;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\OrderDelivery\OrderDeliveryEntity;

interface OrderDeliveryApiServiceInterface
{
    public function addTrackingCode(
        string $trackingCode,
        ?OrderDeliveryEntity $orderDelivery,
        Context $context
    ): void;
}
