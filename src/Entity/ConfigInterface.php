<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

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

    public function getOriginOffice(): ?int;

    public function setOriginOffice(?string $originOffice): void;

    public function getShop(): ShopInterface;

    public function setShop(ShopInterface $shop): void;

    public function getSalesChannelId(): string;

    public function setSalesChannelId(string $salesChannelId): void;
}
