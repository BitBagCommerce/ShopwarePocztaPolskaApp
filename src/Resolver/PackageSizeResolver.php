<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Resolver;

use BitBag\PPClient\Model\PostalPackage;
use BitBag\PPClient\Model\RecordedDelivery;
use BitBag\ShopwarePocztaPolskaApp\Exception\PackageSizeException;

final class PackageSizeResolver implements PackageSizeResolverInterface
{
    public function resolve(
        int $depth,
        int $height,
        int $width
    ): string {
        $packageSize = RecordedDelivery::PACKAGE_SIZE_A;

        $maxDimensions = PostalPackage::PACKAGE_SIZE_B_MAX_DIMENSIONS;
        if ($maxDimensions < $depth || $maxDimensions < $height || $maxDimensions < $width) {
            throw new PackageSizeException('bitbag.shopware_poczta_polska_app.package.weight_too_large');
        }

        if (PostalPackage::PACKAGE_SIZE_A_MAX_DEPTH < $depth ||
            PostalPackage::PACKAGE_SIZE_A_MAX_HEIGHT < $height ||
            PostalPackage::PACKAGE_SIZE_A_MAX_WIDTH < $width
        ) {
            $packageSize = RecordedDelivery::PACKAGE_SIZE_B;
        }

        return $packageSize;
    }
}
