<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Repository;

use BitBag\ShopwarePocztaPolskaApp\Entity\ConfigInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface ConfigRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function getByShopIdAndSalesChannelId(string $shopId, string $salesChannelId): ConfigInterface;

    public function findByShopIdAndSalesChannelId(string $shopId, string $salesChannelId): ?ConfigInterface;
}
