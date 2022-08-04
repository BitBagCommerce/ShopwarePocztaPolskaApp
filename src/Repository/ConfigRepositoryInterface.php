<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Repository;

use BitBag\ShopwarePocztaPolskaApp\Entity\ConfigInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface ConfigRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function getByShopIdAndSalesChannelId(string $shopId, string $salesChannelId): ConfigInterface;

    public function findByShopIdAndSalesChannelId(string $shopId, string $salesChannelId): ?ConfigInterface;
}
