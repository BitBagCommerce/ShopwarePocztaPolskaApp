<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Calculator;

use BitBag\PPClient\Model\PocztexPackageSizeEnum;
use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderWeightException;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\Entity\OrderLineItem\OrderLineItemEntity;
use Vin\ShopwareSdk\Data\Entity\Product\ProductEntity;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class OrderWeightCalculator implements OrderWeightCalculatorInterface
{
    public function __construct(private RepositoryInterface $productRepository)
    {
    }

    public function calculate(OrderEntity $order, Context $context): float
    {
        $totalWeight = 0.0;
        $lineItems = $order->lineItems?->getElements();
        if (null === $lineItems) {
            $lineItems = [];
        }

        $products = array_map(static fn (OrderLineItemEntity $item) => $item->product, $lineItems);
        $products = array_filter($products);
        $parentIds = array_filter($products, static fn (ProductEntity $product) => null !== $product->parentId);

        $searchParentProductsCriteria = (new Criteria())
            ->setIds(array_column($parentIds, 'parentId'));

        $searchParentProducts = $this->productRepository->search($searchParentProductsCriteria, $context);

        $parentProducts = $searchParentProducts->entities->getElements();

        foreach ($lineItems as $item) {
            $product = $item->product;
            $productWeight = 0.0;

            if (null !== $product) {
                $parentId = $product->parentId;
                $productWeight = $product->weight;

                if (null !== $parentId && isset($parentProducts[$parentId])) {
                    /** @var ProductEntity $mainProduct */
                    $mainProduct = $parentProducts[$parentId];

                    $productWeight = $mainProduct->weight;
                }
            }

            $totalWeight += $item->quantity * $productWeight;
        }

        if (0.0 === $totalWeight) {
            throw new OrderWeightException('bitbag.shopware_poczta_polska_app.order.products.null_weight');
        }

        if (PocztexPackageSizeEnum::MAX_WEIGHT_2XL <= $totalWeight) {
            throw new OrderWeightException('bitbag.shopware_poczta_polska_app.order.products.too_heavy');
        }

        return $totalWeight;
    }
}
