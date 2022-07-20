<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Validator;

use BitBag\ShopwarePocztaPolskaApp\Exception\Order\OrderCustomFieldException;
use BitBag\ShopwarePocztaPolskaApp\Resolver\OrderCustomFieldResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

final class OrderCustomFieldValidator
{
    public const DEPTH_INVALID_MESSAGE_KEY = 'bitbag.shopware_poczta_polska_app.order.custom_fields.depth_invalid';

    public const HEIGHT_INVALID_MESSAGE_KEY = 'bitbag.shopware_poczta_polska_app.order.custom_fields.height_invalid';

    public const WIDTH_INVALID_MESSAGE_KEY = 'bitbag.shopware_poczta_polska_app.order.custom_fields.width_invalid';

    public const PACKAGE_CONTENTS_INVALID_MESSAGE_KEY = 'bitbag.shopware_poczta_polska_app.order.custom_fields.package_contents_invalid';

    public const PLANNED_SHIPPING_DATE_INVALID_MESSAGE_KEY = 'bitbag.shopware_poczta_polska_app.order.custom_fields.planned_shipping_date_invalid';

    public function validate(array $data): void
    {
        $packageDetailsKey = OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY;
        $validator = Validation::createValidator();
        $depthKey = $packageDetailsKey . '_depth';
        $heightKey = $packageDetailsKey . '_height';
        $widthKey = $packageDetailsKey . '_width';
        $packageContentsKey = $packageDetailsKey . '_package_contents';
        $plannedShippingDateKey = $packageDetailsKey . '_planned_shipping_date';

        if (!isset($data[$depthKey])) {
            $data[$depthKey] = null;
        }

        if (!isset($data[$heightKey])) {
            $data[$heightKey] = null;
        }

        if (!isset($data[$widthKey])) {
            $data[$widthKey] = null;
        }

        if (!isset($data[$packageContentsKey])) {
            $data[$packageContentsKey] = null;
        }

        if (!isset($data[$plannedShippingDateKey])) {
            $data[$plannedShippingDateKey] = null;
        }

        $constraint = new Assert\Collection([
            $depthKey => [
                new Assert\NotBlank(['message' => self::DEPTH_INVALID_MESSAGE_KEY]),
                new Assert\Length(['min' => 1, 'minMessage' => self::DEPTH_INVALID_MESSAGE_KEY]),
            ],
            $heightKey => [
                new Assert\NotBlank(['message' => self::HEIGHT_INVALID_MESSAGE_KEY]),
                new Assert\Length(['min' => 1, 'minMessage' => self::HEIGHT_INVALID_MESSAGE_KEY]),
            ],
            $widthKey => [
                new Assert\NotBlank(['message' => self::WIDTH_INVALID_MESSAGE_KEY]),
                new Assert\Length(['min' => 1, 'minMessage' => self::WIDTH_INVALID_MESSAGE_KEY]),
            ],
            $packageContentsKey => new Assert\NotBlank([
                'message' => self::PACKAGE_CONTENTS_INVALID_MESSAGE_KEY,
            ]),
            $plannedShippingDateKey => new Assert\NotBlank([
                'message' => self::PLANNED_SHIPPING_DATE_INVALID_MESSAGE_KEY,
            ]),
        ]);

        $violations = $validator->validate($data, $constraint);
        if (0 !== $violations->count()) {
            throw new OrderCustomFieldException((string) $violations->get(0)->getMessage());
        }
    }
}
