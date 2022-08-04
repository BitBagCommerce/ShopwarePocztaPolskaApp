<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Validator;

use BitBag\PPClient\Model\OriginOffice;
use BitBag\ShopwarePocztaPolskaApp\Entity\ConfigInterface;
use BitBag\ShopwarePocztaPolskaApp\Exception\ConfigException;
use BitBag\ShopwarePocztaPolskaApp\Repository\ConfigRepositoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\ApiResolverInterface;

final class ConfigValidator implements ConfigValidatorInterface
{
    public function __construct(
        private ConfigRepositoryInterface $configRepository,
        private ApiResolverInterface $apiResolver
    ) {
    }

    public function validate(?ConfigInterface $config): void
    {
        if (null === $config) {
            throw new ConfigException('bitbag.shopware_poczta_polska_app.config.not_found');
        }

        if (null === $config->getOriginOffice()) {
            throw new ConfigException('bitbag.shopware_poczta_polska_app.config.origin_offices.empty');
        }

        $this->validateOriginOffice($config);
    }

    private function validateOriginOffice(ConfigInterface $config): void
    {
        $client = $this->apiResolver->getClient($config->getShop()->getShopId(), $config->getSalesChannelId());
        $originOffices = $client->getOriginOffice()->getOriginOffices();

        $originOffice = array_filter($originOffices, static fn (OriginOffice $originOffice) => $config->getOriginOffice() === $originOffice->getId());
        if ([] === $originOffice) {
            throw new ConfigException('bitbag.shopware_poczta_polska_app.config.origin_offices.invalid');
        }
    }
}
