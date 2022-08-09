<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Api;

use BitBag\PPClient\Client\PPClientInterface;
use BitBag\PPClient\Model\AddDeliveryResponseItem;
use BitBag\PPClient\Model\Packet;
use BitBag\PPClient\Model\Request\PocztexDeliveryRequest;
use BitBag\PPClient\Model\Request\SendEnvelopeRequest;
use BitBag\ShopwarePocztaPolskaApp\Exception\PackageException;
use BitBag\ShopwarePocztaPolskaApp\Factory\Package\AddressFactoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Factory\Package\PackageFactoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\ApiResolverInterface;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class PackageApiService implements PackageApiServiceInterface
{
    public function __construct(
        private AddressFactoryInterface $addressFactory,
        private PackageFactoryInterface $packageFactory,
        private ApiResolverInterface $apiResolver,
        private DocumentApiServiceInterface $documentApiService
    ) {
    }

    public function createPackage(
        string $shopId,
        int $originOffice,
        OrderEntity $order,
        Context $context,
        PPClientInterface $client
    ): AddDeliveryResponseItem {
        $address = $this->addressFactory->create(
            $order->deliveries?->first()->shippingOrderAddress,
            $order->orderCustomer?->email
        );
        $package = $this->packageFactory->create(
            $order,
            $address,
            $context
        );

        $client->clearEnvelope();

        $shipmentRequest = new PocztexDeliveryRequest();
        $shipmentRequest->setPackages([$package]);
        $shipment = $client->addPocztexDelivery($shipmentRequest);

        $firstPackageResponse = $shipment->getAddDeliveryResponseItems()[0];
        if ([] !== $firstPackageResponse->getErrors()) {
            throw new PackageException($firstPackageResponse->getErrors()[0]->getErrorDesc());
        }

        $this->documentApiService->uploadOrderLabel(
            $package->getGuid(),
            $order->id,
            $order->orderNumber,
            $client,
            $context
        );

        $this->sendPackage(
            $package->getGuid(),
            $originOffice,
            $client
        );

        return $firstPackageResponse;
    }

    private function sendPackage(
        string $packageGuid,
        int $originOffice,
        PPClientInterface $client
    ): void {
        $packet = new Packet();
        $packet->setGuid($packageGuid);

        $envelopeRequest = new SendEnvelopeRequest();
        $envelopeRequest->setPacket($packet);
        $envelopeRequest->setParcelOriginOffice($originOffice);

        $sendEnvelope = $client->sendEnvelope($envelopeRequest);
        if ([] !== $sendEnvelope->getErrors()) {
            throw new PackageException($sendEnvelope->getErrors()[0]->getErrorDesc());
        }
    }
}
