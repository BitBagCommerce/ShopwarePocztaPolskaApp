<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Controller;

use BitBag\ShopwareAppSystemBundle\Exception\ShopNotFoundException;
use BitBag\ShopwareAppSystemBundle\Factory\Context\ContextFactoryInterface;
use BitBag\ShopwareAppSystemBundle\Model\Action\ActionInterface;
use BitBag\ShopwareAppSystemBundle\Model\Feedback\NewTab;
use BitBag\ShopwareAppSystemBundle\Repository\ShopRepositoryInterface;
use BitBag\ShopwareAppSystemBundle\Response\FeedbackResponse;
use BitBag\ShopwareAppSystemBundle\Service\DocumentServiceInterface;
use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderDocumentNotFoundException;
use BitBag\ShopwarePocztaPolskaApp\Factory\FeedbackResponseFactoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Finder\OrderFinderInterface;
use BitBag\ShopwarePocztaPolskaApp\Validator\OrderValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Document\DocumentEntity;

final class LabelController extends AbstractController
{
    public function __construct(
        private FeedbackResponseFactoryInterface $feedbackResponseFactory,
        private ContextFactoryInterface $contextFactory,
        private ShopRepositoryInterface $shopRepository,
        private DocumentServiceInterface $documentService,
        private OrderFinderInterface $orderFinder,
        private OrderValidatorInterface $orderValidator
    ) {
    }

    public function getLabel(ActionInterface $action, Context $context): Response
    {
        $orderId = $action->getData()->getIds()[0] ?? '';
        $order = $this->orderFinder->getWithAssociations($orderId, $context);

        $shopId = $action->getSource()->getShopId();

        try {
            $this->orderValidator->validate(
                $shopId,
                $order,
                $context
            );
        } catch (\Exception $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        $redirectUrl = $this->generateUrl(
            'show_label',
            ['orderId' => $orderId],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new FeedbackResponse(new NewTab($redirectUrl));
    }

    public function showLabel(Request $request): Response
    {
        $data = $request->query->all();
        $shopId = $data['shop-id'] ?? '';

        $shop = $this->shopRepository->find($shopId);
        if (null === $shop) {
            throw new ShopNotFoundException($shopId);
        }

        $context = $this->contextFactory->create($shop);
        if (null === $context) {
            throw new UnauthorizedHttpException('');
        }

        $orderId = $data['orderId'] ?? '';
        $order = $this->orderFinder->getWithAssociations($orderId, $context);

        /** @var DocumentEntity|null $orderDocument */
        $orderDocument = $order->documents?->last();
        if (null === $orderDocument) {
            throw new OrderDocumentNotFoundException('bitbag.shopware_poczta_polska_app.package.not_found');
        }

        $labelContent = $this->documentService->downloadDocument(
            $orderDocument->id,
            $orderDocument->deepLinkCode,
            $context
        );

        $filename = sprintf('filename="label_%s.pdf"', 'order_' . $orderId);

        $response = new Response($labelContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Content-Disposition', $filename);

        return $response;
    }
}
