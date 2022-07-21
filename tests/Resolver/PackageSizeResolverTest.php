<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Resolver;

use BitBag\PPClient\Model\RecordedDelivery;
use BitBag\ShopwarePocztaPolskaApp\Resolver\PackageSizeResolver;
use PHPUnit\Framework\TestCase;

final class PackageSizeResolverTest extends TestCase
{
    public function testCreateSizeA(): void
    {
        self::assertEquals(
            RecordedDelivery::PACKAGE_SIZE_A,
            (new PackageSizeResolver())->resolve(10, 10, 10)
        );
    }

    public function testCreateSizeB(): void
    {
        self::assertEquals(
            RecordedDelivery::PACKAGE_SIZE_B,
            (new PackageSizeResolver())->resolve(55, 55, 55)
        );
    }
}
