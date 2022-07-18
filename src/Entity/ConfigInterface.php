<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Entity;

use BitBag\ShopwareAppSystemBundle\Entity\ShopInterface;

interface ConfigInterface
{
    public const SANDBOX_ENVIRONMENT = 'sandbox';

    public const PRODUCTION_ENVIRONMENT = 'production';

    public function getId(): ?int;

    public function getApiLogin(): string;

    public function setApiLogin(string $apiLogin): void;

    public function getApiPassword(): string;

    public function setApiPassword(string $apiPassword): void;

    public function getApiEnvironment(): string;

    public function setApiEnvironment(string $apiEnvironment): void;

    public function getOfficeOrigin(): string;

    public function setOfficeOrigin(string $officeOrigin): void;

    public function getShop(): ShopInterface;

    public function setShop(ShopInterface $shop): void;

    public function getSalesChannelId(): string;

    public function setSalesChannelId(string $salesChannelId): void;
}
