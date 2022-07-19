<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Resolver;

interface PackageSizeResolverInterface
{
    public const PACKAGE_SIZE_A_MAX_DEPTH = 60;

    public const PACKAGE_SIZE_A_MAX_WIDTH = 50;

    public const PACKAGE_SIZE_A_MAX_HEIGHT = 30;

    public const PACKAGE_SIZE_B_MAX_DIMENSIONS = 150;

    public const MAX_WEIGHT_PACKAGE = 10;

    public function resolve(
        int $depth,
        int $height,
        int $width
    ): string;
}
