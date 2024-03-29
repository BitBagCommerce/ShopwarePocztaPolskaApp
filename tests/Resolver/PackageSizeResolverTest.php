<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Resolver;

use BitBag\PPClient\Model\PocztexPackageSizeEnum;
use BitBag\ShopwarePocztaPolskaApp\Resolver\PackageSizeResolver;
use PHPUnit\Framework\TestCase;

final class PackageSizeResolverTest extends TestCase
{
    public function testCreateMinSizeS(): void
    {
        self::assertEquals(
            PocztexPackageSizeEnum::S,
            (new PackageSizeResolver())->resolve(10, 1, 10, 10)
        );
    }

    public function testCreateMaxSizeS(): void
    {
        self::assertEquals(
            PocztexPackageSizeEnum::S,
            (new PackageSizeResolver())->resolve(10, 9, 10, 10)
        );
    }

    public function testCreateMinSizeM(): void
    {
        self::assertEquals(
            PocztexPackageSizeEnum::M,
            (new PackageSizeResolver())->resolve(10, 10, 10, 10)
        );
    }

    public function testCreateMaxSizeM(): void
    {
        self::assertEquals(
            PocztexPackageSizeEnum::M,
            (new PackageSizeResolver())->resolve(10, 20, 10, 10)
        );
    }

    public function testCreateMinSizeL(): void
    {
        self::assertEquals(
            PocztexPackageSizeEnum::L,
            (new PackageSizeResolver())->resolve(10, 21, 10, 10)
        );
    }

    public function testCreateMaxSizeL(): void
    {
        self::assertEquals(
            PocztexPackageSizeEnum::L,
            (new PackageSizeResolver())->resolve(10, 42, 10, 10)
        );
    }

    public function testCreateMinSizeXL(): void
    {
        self::assertEquals(
            PocztexPackageSizeEnum::XL,
            (new PackageSizeResolver())->resolve(10, 43, 10, 10)
        );
    }

    public function testCreateMaxSizeXL(): void
    {
        self::assertEquals(
            PocztexPackageSizeEnum::XL,
            (new PackageSizeResolver())->resolve(10, 60, 10, 10)
        );
    }

    public function testCreateMinSizeXXL(): void
    {
        self::assertEquals(
            PocztexPackageSizeEnum::DOUBLE_XL,
            (new PackageSizeResolver())->resolve(100, 61, 50, 10)
        );
    }

    public function testCreateMaxSizeXXL(): void
    {
        self::assertEquals(
            PocztexPackageSizeEnum::DOUBLE_XL,
            (new PackageSizeResolver())->resolve(100, 50, 100, 10)
        );
    }

    public function testExceptionPackageTooLarge(): void
    {
        $this->expectExceptionMessage('bitbag.shopware_poczta_polska_app.package.too_large');

        (new PackageSizeResolver())->resolve(100, 51, 100, 10);
    }
}
