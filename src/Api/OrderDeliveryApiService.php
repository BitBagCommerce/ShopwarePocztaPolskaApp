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
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class OrderDeliveryApiService implements OrderDeliveryApiServiceInterface
{
    public function __construct(private RepositoryInterface $orderDeliveryRepository)
    {
    }

    public function addTrackingCode(
        string $trackingCode,
        ?OrderDeliveryEntity $orderDelivery,
        Context $context
    ): void {
        if (null === $orderDelivery) {
            return;
        }

        $trackingCodes = $orderDelivery->trackingCodes ?? [];

        if (!in_array($trackingCode, $trackingCodes)) {
            $this->orderDeliveryRepository->update([
                'id' => $orderDelivery->id,
                'trackingCodes' => array_merge($trackingCodes, [$trackingCode]),
            ], $context);
        }
    }
}
