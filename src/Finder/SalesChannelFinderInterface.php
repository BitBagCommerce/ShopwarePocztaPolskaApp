<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Finder;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Repository\Struct\EntitySearchResult;

interface SalesChannelFinderInterface
{
    public function findAll(Context $context): EntitySearchResult;

    public function findById(string $id, Context $context): EntitySearchResult;
}
