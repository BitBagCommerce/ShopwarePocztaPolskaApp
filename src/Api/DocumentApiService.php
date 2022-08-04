<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Api;

use BitBag\PPClient\Client\PPClientInterface;
use BitBag\PPClient\Model\Request\LabelRequest;
use BitBag\ShopwareAppSystemBundle\Service\DocumentServiceInterface;
use BitBag\ShopwarePocztaPolskaApp\Exception\LabelException;
use Vin\ShopwareSdk\Data\Context;

final class DocumentApiService implements DocumentApiServiceInterface
{
    public function __construct(private DocumentServiceInterface $documentService)
    {
    }

    public function uploadOrderLabel(
        string $packageGuid,
        string $orderId,
        string $orderNumber,
        PPClientInterface $client,
        Context $context
    ): void {
        $labelRequest = new LabelRequest();
        $labelRequest->setGuid($packageGuid);

        $label = $client->getLabel($labelRequest);
        if ([] !== $label->getErrors()) {
            throw new LabelException($label->getErrors()[0]->getErrorDesc());
        }

        $createdDocumentResponse = $this->documentService->createDocument(
            $orderId,
            'delivery_note',
            $context
        );

        $documentId = $createdDocumentResponse->getContents()['documentId'];
        $labelContent = $label->getAddressLabels()[0]->getPdfContent();

        $this->documentService->uploadDocument(
            $documentId,
            "bitbag_shopware_poczta_polska_app_$orderNumber",
            $labelContent,
            $context,
            'pdf'
        );
    }
}
