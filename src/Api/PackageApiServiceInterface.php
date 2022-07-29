<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Api;

use BitBag\PPClient\Client\PPClientInterface;
use BitBag\PPClient\Model\AddShipmentResponseItem;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface PackageApiServiceInterface
{
    public function createPackage(
        string $shopId,
        int $originOffice,
        OrderEntity $order,
        Context $context,
        PPClientInterface $client
    ): AddShipmentResponseItem;
}
