<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Controller;

use BitBag\ShopwareAppSystemBundle\Exception\ShopNotFoundException;
use BitBag\ShopwareAppSystemBundle\Repository\ShopRepositoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Entity\Config;
use BitBag\ShopwarePocztaPolskaApp\Repository\ConfigRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class ConfigController extends AbstractController
{
    public function __construct(
        private ShopRepositoryInterface $shopRepository,
        private ConfigRepositoryInterface $configRepository
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $shopId = $request->query->get('shopId', '');
        $salesChannelId = $request->query->get('salesChannelId', '');

        $shop = $this->shopRepository->find($shopId);

        if (null === $shop) {
            throw new ShopNotFoundException($shopId);
        }

        $config = $this->configRepository->findByShopIdAndSalesChannelId($shopId, $salesChannelId) ?? new Config();

        return $this->json($config);
    }
}
