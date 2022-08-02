<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\EntityDefinition;

use BitBag\ShopwareAppSystemBundle\EntityDefinition\AbstractCustomEntityDefinition;

class PackageDefinition extends AbstractCustomEntityDefinition
{
    public function getEntityName(): string
    {
        return 'custom_entity_bitbag_shopware_poczta_polska_app_packages';
    }
}
