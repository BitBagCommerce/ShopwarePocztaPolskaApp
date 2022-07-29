<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Validator;

use BitBag\ShopwarePocztaPolskaApp\Entity\ConfigInterface;
use BitBag\ShopwarePocztaPolskaApp\Exception\ConfigException;
use BitBag\ShopwarePocztaPolskaApp\Repository\ConfigRepositoryInterface;

final class ConfigValidator implements ConfigValidatorInterface
{
    public function __construct(private ConfigRepositoryInterface $configRepository)
    {
    }

    public function validate(?ConfigInterface $config): void
    {
        if (null === $config) {
            throw new ConfigException('bitbag.shopware_poczta_polska_app.config.not_found');
        }

        if (null === $config->getOriginOffice()) {
            throw new ConfigException('bitbag.shopware_poczta_polska_app.config.origin_offices.empty');
        }
    }
}
