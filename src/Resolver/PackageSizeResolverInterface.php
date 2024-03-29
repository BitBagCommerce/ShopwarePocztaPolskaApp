<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Resolver;

interface PackageSizeResolverInterface
{
    public function resolve(
        int $depth,
        int $height,
        int $width,
        int $weight
    ): string;
}
