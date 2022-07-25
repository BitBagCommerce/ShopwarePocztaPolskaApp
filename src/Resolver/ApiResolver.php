<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Resolver;

use BitBag\PPClient\Client\PPClient;
use BitBag\PPClient\Client\PPClientConfiguration;
use BitBag\PPClient\Factory\Client\SoapClientFactory;
use BitBag\PPClient\Factory\Response\AddShipmentResponseFactory;
use BitBag\PPClient\Factory\Response\ClearEnvelopeResponseFactory;
use BitBag\PPClient\Factory\Response\GetLabelResponseFactory;
use BitBag\PPClient\Factory\Response\GetOriginOfficeResponseFactory;
use BitBag\PPClient\Factory\Response\SendEnvelopeResponseFactory;
use BitBag\PPClient\Normalizer\ArrayNormalizer;
use BitBag\ShopwarePocztaPolskaApp\Repository\ConfigRepositoryInterface;

final class ApiResolver implements ApiResolverInterface
{
    public function __construct(private ConfigRepositoryInterface $configRepository)
    {
    }

    public function getClient(string $shopId, string $salesChannelId): PPClient
    {
        $config = $this->configRepository->getByShopIdAndSalesChannelId($shopId, $salesChannelId);
        $arrayNormalizer = new ArrayNormalizer();
        $soapClientFactory = new SoapClientFactory();
        $ppClientConfiguration = new PPClientConfiguration(
            __DIR__ . '/../../vendor/bitbag/pp-client/src/Resources/client_dev.wsdl',
            $config->getApiLogin(),
            $config->getApiPassword(),
        );

        return new PPClient(
            $soapClientFactory->create($ppClientConfiguration),
            new AddShipmentResponseFactory(),
            new ClearEnvelopeResponseFactory($arrayNormalizer),
            new GetLabelResponseFactory($arrayNormalizer),
            new SendEnvelopeResponseFactory($arrayNormalizer),
            new GetOriginOfficeResponseFactory()
        );
    }
}
