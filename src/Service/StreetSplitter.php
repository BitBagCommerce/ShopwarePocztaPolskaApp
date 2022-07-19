<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Service;

use BitBag\ShopwarePocztaPolskaApp\Exception\StreetCannotBeSplitException;

final class StreetSplitter implements StreetSplitterInterface
{
    public function splitStreet(string $street): array
    {
        if (!preg_match('/^(.+)\s(\d.*)/', $street, $streetAddress)) {
            throw new StreetCannotBeSplitException('bitbag.shopware_poczta_polska_app.order_address.invalid_street');
        }

        return $streetAddress;
    }
}
