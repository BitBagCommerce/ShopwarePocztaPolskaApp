<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Service;

interface StreetSplitterInterface
{
    public function splitStreet(string $street): array;
}
