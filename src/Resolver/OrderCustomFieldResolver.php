<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Resolver;

use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderCustomFieldException;
use BitBag\ShopwarePocztaPolskaApp\Model\OrderCustomFieldModel;
use BitBag\ShopwarePocztaPolskaApp\Validator\OrderCustomFieldValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class OrderCustomFieldResolver implements OrderCustomFieldResolverInterface
{
    public function __construct(private OrderCustomFieldValidatorInterface $orderCustomFieldValidator)
    {
        $this->orderCustomFieldValidator = $orderCustomFieldValidator;
    }

    public function resolve(OrderEntity $order): OrderCustomFieldModel
    {
        $packageDetailsKey = self::PACKAGE_DETAILS_KEY;
        /** @psalm-var array<array-key, mixed>|null */
        $orderCustomFields = $order->getCustomFields() ?? [];

        $violations = $this->orderCustomFieldValidator->validate($orderCustomFields);
        if (0 !== $violations->count()) {
            $orderCustomFieldsMessage = '';

            /** @var ConstraintViolationInterface $violation */
            foreach ($violations as $violation) {
                $orderCustomFieldsMessage .= $violation->getMessage() . '. <br />';
            }

            throw new OrderCustomFieldException($orderCustomFieldsMessage);
        }

        $depthKey = $packageDetailsKey . '_depth';
        $heightKey = $packageDetailsKey . '_height';
        $widthKey = $packageDetailsKey . '_width';
        $packageContentsKey = $packageDetailsKey . '_package_contents';
        $plannedShippingDate = $packageDetailsKey . '_planned_shipping_date';

        return new OrderCustomFieldModel(
            $orderCustomFields[$depthKey],
            $orderCustomFields[$heightKey],
            $orderCustomFields[$widthKey],
            $orderCustomFields[$packageContentsKey],
            $orderCustomFields[$plannedShippingDate]
        );
    }
}
