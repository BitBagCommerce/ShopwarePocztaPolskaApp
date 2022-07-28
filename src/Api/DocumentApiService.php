<?php

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

    public function addLabelToOrderDocument(
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
            "bitbag_shopware_poczta_polska_app_$orderNumber" . time(),
            $labelContent,
            $context,
            'pdf'
        );
    }
}
