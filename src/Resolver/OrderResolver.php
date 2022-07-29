<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Resolver;

use BitBag\ShopwarePocztaPolskaApp\Finder\OrderFinderInterface;
use BitBag\ShopwarePocztaPolskaApp\Validator\OrderValidatorInterface;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class OrderResolver implements OrderResolverInterface
{
    public function __construct(
        private OrderFinderInterface $orderFinder,
        private OrderValidatorInterface $orderValidator
    ) {
    }

    public function getOrder(
        string $orderId,
        string $shopId,
        Context $context
    ): OrderEntity {
        $order = $this->orderFinder->getWithAssociations($orderId, $context);

        $this->orderValidator->validate(
            $shopId,
            $order,
            $context
        );

        return $order;
    }
}
