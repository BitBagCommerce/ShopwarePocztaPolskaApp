<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Resolver;

use BitBag\ShopwarePocztaPolskaApp\Entity\ConfigInterface;
use BitBag\ShopwarePocztaPolskaApp\Repository\ConfigRepositoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Validator\ConfigValidatorInterface;

final class ConfigResolver implements ConfigResolverInterface
{
    public function __construct(
        private ConfigRepositoryInterface $configRepository,
        private ConfigValidatorInterface $configValidator
    ) {
    }

    public function getConfig(string $shopId, string $salesChannelId): ConfigInterface
    {
        /** @var ConfigInterface|null $config */
        $config = $this->configRepository->findByShopIdAndSalesChannelId($shopId, $salesChannelId);
        if (null === $config) {
            /** @var ConfigInterface $config */
            $config = $this->configRepository->findByShopIdAndSalesChannelId($shopId, '');
        }

        $this->configValidator->validate($config);

        return $config;
    }
}
