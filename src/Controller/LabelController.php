<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Controller;

use BitBag\ShopwareAppSystemBundle\Model\Action\ActionInterface;
use BitBag\ShopwareAppSystemBundle\Model\Feedback\NewTab;
use BitBag\ShopwareAppSystemBundle\Response\FeedbackResponse;
use BitBag\ShopwareAppSystemBundle\Service\DocumentServiceInterface;
use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderDocumentNotFoundException;
use BitBag\ShopwarePocztaPolskaApp\Factory\FeedbackResponseFactoryInterface;
use BitBag\ShopwarePocztaPolskaApp\Finder\OrderFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Entity\Document\DocumentEntity;
use Vin\ShopwareSdk\Data\Filter\EqualsFilter;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class LabelController extends AbstractController
{
    public function __construct(
        private FeedbackResponseFactoryInterface $feedbackResponseFactory,
        private DocumentServiceInterface $documentService,
        private OrderFinderInterface $orderFinder,
        private RepositoryInterface $packageRepository
    ) {
    }

    public function getLabel(ActionInterface $action, Context $context): Response
    {
        $orderId = $action->getData()->getIds()[0] ?? '';
        $order = $this->orderFinder->getWithAssociations($orderId, $context);

        $shippingMethod = $order->deliveries?->first()?->shippingMethod ?? null;
        if (null === $shippingMethod) {
            return $this->feedbackResponseFactory->createError('bitbag.shopware_poczta_polska_app.order.shipping_method.not_found');
        }

        $technicalName = $shippingMethod->getTranslated()['customFields']['technical_name'] ?? null;
        if (CreatePackageController::SHIPPING_KEY !== $technicalName) {
            return $this->feedbackResponseFactory->createError('bitbag.shopware_poczta_polska_app.order.shipping_method.not_polish_post');
        }

        $packageCriteria = (new Criteria())->addFilter(new EqualsFilter('order.id', $order->id));
        $package = $this->packageRepository->searchIds($packageCriteria, $context);
        if (0 === $package->getTotal()) {
            return $this->feedbackResponseFactory->createError('bitbag.shopware_poczta_polska_app.package.not_found');
        }

        $redirectUrl = $this->generateUrl(
            'show_label',
            ['orderId' => $orderId],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new FeedbackResponse(new NewTab($redirectUrl));
    }

    public function showLabel(Request $request, Context $context): Response
    {
        $data = $request->query->all();

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

        $filename = sprintf('filename="label_%s.pdf"', 'order_' . $order->orderNumber);

        $response = new Response($labelContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Content-Disposition', $filename);

        return $response;
    }
}
