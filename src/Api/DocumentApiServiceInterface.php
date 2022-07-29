<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Api;

use BitBag\PPClient\Client\PPClientInterface;
use Vin\ShopwareSdk\Data\Context;

interface DocumentApiServiceInterface
{
    public function uploadOrderLabel(
        string $packageGuid,
        string $orderId,
        string $orderNumber,
        PPClientInterface $client,
        Context $context
    ): void;
}
