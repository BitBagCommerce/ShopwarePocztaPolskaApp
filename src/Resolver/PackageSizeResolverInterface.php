<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Resolver;

interface PackageSizeResolverInterface
{
    public function resolve(
        int $depth,
        int $height,
        int $width
    ): string;
}
