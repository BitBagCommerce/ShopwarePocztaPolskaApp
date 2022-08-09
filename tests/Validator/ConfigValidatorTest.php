<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Validator;

use BitBag\ShopwareAppSystemBundle\Entity\Shop;
use BitBag\ShopwarePocztaPolskaApp\Entity\Config;
use BitBag\ShopwarePocztaPolskaApp\Repository\ConfigRepositoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\ApiResolverInterface;
use BitBag\ShopwarePocztaPolskaApp\Validator\ConfigValidator;
use PHPUnit\Framework\TestCase;

final class ConfigValidatorTest extends TestCase
{
    public function testValidateNullConfig(): void
    {
        $this->expectExceptionMessage('bitbag.shopware_poczta_polska_app.config.not_found');

        $configRepository = $this->createMock(ConfigRepositoryInterface::class);
        $apiResolver = $this->createMock(ApiResolverInterface::class);
        $validator = new ConfigValidator($configRepository, $apiResolver);

        $validator->validate(null);
    }

    public function testValidateNullOriginOffice(): void
    {
        $this->expectExceptionMessage('bitbag.shopware_poczta_polska_app.config.origin_offices.empty');

        $configRepository = $this->createMock(ConfigRepositoryInterface::class);
        $apiResolver = $this->createMock(ApiResolverInterface::class);
        $validator = new ConfigValidator($configRepository, $apiResolver);

        $config = $this->createConfig();
        $config->setOriginOffice(null);

        $validator->validate($config);
    }

    private function createConfig(): Config
    {
        $shop = new Shop();

        $config = new Config();
        $config->setApiLogin('foo');
        $config->setApiPassword('bar');
        $config->setOriginOffice('1234');
        $config->setApiEnvironment('dev');
        $config->setSalesChannelId('7890');
        $config->setShop($shop);

        return $config;
    }
}
