<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Resolver;

use BitBag\ShopwarePocztaPolskaApp\Entity\ConfigInterface;

interface ConfigResolverInterface
{
    public function getConfig(string $shopId, string $salesChannelId): ConfigInterface;
}
