<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

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
