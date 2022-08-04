<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

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
