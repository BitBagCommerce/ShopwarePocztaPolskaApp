<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Finder;

use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderException;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\Filter\EqualsFilter;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class OrderFinder implements OrderFinderInterface
{
    public function __construct(private RepositoryInterface $orderRepository)
    {
    }

    public function getWithAssociations(?string $orderId, Context $context): OrderEntity
    {
        $orderCriteria = (new Criteria())->addFilter(new EqualsFilter('id', $orderId));
        $orderCriteria->addAssociations([
            'lineItems.product',
            'deliveries.shippingMethod',
            'addresses',
            'transactions',
            'transactions.paymentMethod',
            'documents',
        ]);

        $searchOrder = $this->orderRepository->search($orderCriteria, $context);

        /** @var OrderEntity|null $order */
        $order = $searchOrder->first();
        if (null === $order) {
            throw new OrderException('bitbag.shopware_poczta_polska_app.order.not_found');
        }

        return $order;
    }
}
