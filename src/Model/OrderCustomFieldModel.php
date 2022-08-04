<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Model;

final class OrderCustomFieldModel
{
    public function __construct(
        private string | int $depth,
        private string | int $height,
        private string | int $width,
        private string $packageContents,
        private string $plannedShippingDate
    ) {
    }

    public function getDepth(): int
    {
        return (int) $this->depth;
    }

    public function getHeight(): int
    {
        return (int) $this->height;
    }

    public function getWidth(): int
    {
        return (int) $this->width;
    }

    public function getPackageContents(): string
    {
        return $this->packageContents;
    }

    public function getPlannedShippingDate(): string
    {
        return $this->plannedShippingDate;
    }
}
