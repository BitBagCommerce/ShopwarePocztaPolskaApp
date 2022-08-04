<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Entity;

use BitBag\ShopwareAppSystemBundle\Entity\ShopInterface;

/**
 * @psalm-suppress MissingConstructor
 */
class Config implements ConfigInterface
{
    protected ?int $id;

    protected string $apiLogin;

    protected string $apiPassword;

    protected string $apiEnvironment;

    protected ?string $originOffice;

    protected string $salesChannelId;

    protected ShopInterface $shop;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApiLogin(): string
    {
        return $this->apiLogin;
    }

    public function setApiLogin(string $apiLogin): void
    {
        $this->apiLogin = $apiLogin;
    }

    public function getApiPassword(): string
    {
        return $this->apiPassword;
    }

    public function setApiPassword(string $apiPassword): void
    {
        $this->apiPassword = $apiPassword;
    }

    public function getApiEnvironment(): string
    {
        return $this->apiEnvironment;
    }

    public function setApiEnvironment(string $apiEnvironment): void
    {
        $this->apiEnvironment = $apiEnvironment;
    }

    public function getOriginOffice(): ?int
    {
        return $this->originOffice ? (int) $this->originOffice : null;
    }

    public function setOriginOffice(?string $originOffice): void
    {
        $this->originOffice = $originOffice;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function setSalesChannelId(string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }

    public function getShop(): ShopInterface
    {
        return $this->shop;
    }

    public function setShop(ShopInterface $shop): void
    {
        $this->shop = $shop;
    }
}
