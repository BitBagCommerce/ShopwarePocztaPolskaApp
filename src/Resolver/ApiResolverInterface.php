<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Resolver;

use BitBag\PPClient\Client\PPClient;

interface ApiResolverInterface
{
    public function getClient(string $shopId, string $salesChannelId): PPClient;
}
