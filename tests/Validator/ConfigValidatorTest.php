<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Tests\Validator;

use BitBag\ShopwareAppSystemBundle\Entity\Shop;
use BitBag\ShopwarePocztaPolskaApp\Entity\Config;
use BitBag\ShopwarePocztaPolskaApp\Repository\ConfigRepositoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Validator\ConfigValidator;
use PHPUnit\Framework\TestCase;

final class ConfigValidatorTest extends TestCase
{
    public function testValidateCorrectConfig(): void
    {
        $configRepository = $this->createMock(ConfigRepositoryInterface::class);
        $validator = new ConfigValidator($configRepository);

        $validator->validate($this->createConfig());

        self::assertTrue(true);
    }

    public function testValidateNullConfig(): void
    {
        $this->expectExceptionMessage('bitbag.shopware_poczta_polska_app.config.not_found');

        $configRepository = $this->createMock(ConfigRepositoryInterface::class);
        $validator = new ConfigValidator($configRepository);

        $validator->validate(null);

        self::assertTrue(true);
    }

    public function testValidateNullOriginOffice(): void
    {
        $this->expectExceptionMessage('bitbag.shopware_poczta_polska_app.config.origin_offices.empty');

        $configRepository = $this->createMock(ConfigRepositoryInterface::class);
        $validator = new ConfigValidator($configRepository);

        $config = $this->createConfig();
        $config->setOriginOffice(null);

        $validator->validate($config);

        self::assertTrue(true);
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
