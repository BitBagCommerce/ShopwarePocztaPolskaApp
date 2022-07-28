<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Provider;

final class Defaults
{
    public const SHIPPING_KEY = 'Poczta Polska';

    public static function generateGuid(): string
    {
        return strtoupper(md5(uniqid((string) random_int(32, 32), true)));
    }
}
