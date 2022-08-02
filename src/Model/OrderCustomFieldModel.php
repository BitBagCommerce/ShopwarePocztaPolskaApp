<?php

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
