<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Api;

use BitBag\PPClient\Client\PPClientInterface;
use BitBag\PPClient\Model\AddShipmentResponseItem;
use BitBag\PPClient\Model\Packet;
use BitBag\PPClient\Model\Request\SendEnvelopeRequest;
use BitBag\PPClient\Model\Request\ShipmentRequest;
use BitBag\ShopwarePocztaPolskaApp\Exception\PackageException;
use BitBag\ShopwarePocztaPolskaApp\Factory\Package\AddressFactoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Factory\Package\PostalPackageFactoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Resolver\ApiResolverInterface;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class PackageApiService implements PackageApiServiceInterface
{
    public function __construct(
        private AddressFactoryInterface $addressFactory,
        private PostalPackageFactoryInterface $postalPackageFactory,
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
    ): AddShipmentResponseItem {
        $address = $this->addressFactory->create(
            $order->deliveries?->first()->shippingOrderAddress,
            $order->orderCustomer?->email
        );
        $package = $this->postalPackageFactory->create(
            $order,
            $address,
            $context
        );

        $client->clearEnvelope();

        $shipmentRequest = new ShipmentRequest();
        $shipmentRequest->setPackages([$package]);
        $shipment = $client->addShipment($shipmentRequest);

        $firstPackageResponse = $shipment->getAddShipmentResponseItems()[0];
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
