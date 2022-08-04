<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Resolver;

use BitBag\ShopwarePocztaPolskaApp\Model\OrderCustomFieldModel;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface OrderCustomFieldResolverInterface
{
    public const PACKAGE_DETAILS_KEY = 'bitbag_shopware_poczta_polska_app_package_details';

    public function resolve(OrderEntity $order): OrderCustomFieldModel;
}
