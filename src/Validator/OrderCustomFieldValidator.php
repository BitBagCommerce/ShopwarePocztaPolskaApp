<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Validator;

use BitBag\ShopwarePocztaPolskaApp\Resolver\OrderCustomFieldResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderCustomFieldValidator implements OrderCustomFieldValidatorInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function validate(array $data): ConstraintViolationListInterface
    {
        $depthKey = OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_depth';
        $heightKey = OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_height';
        $widthKey = OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_width';
        $packageContentsKey = OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_package_contents';
        $plannedShippingDateKey = OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_planned_shipping_date';

        $data[$depthKey] ??= null;
        $data[$heightKey] ??= null;
        $data[$widthKey] ??= null;
        $data[$packageContentsKey] ??= null;
        $data[$plannedShippingDateKey] ??= null;

        $constraint = new Assert\Collection([
            $depthKey => [
                new Assert\NotBlank([
                    'message' => $this->translator->trans('bitbag.shopware_poczta_polska_app.order.custom_fields.depth_invalid'),
                ]),
                new Assert\Length([
                    'min' => 1,
                    'minMessage' => $this->translator->trans('bitbag.shopware_poczta_polska_app.order.custom_fields.depth_invalid'),
                ]),
            ],
            $heightKey => [
                new Assert\NotBlank([
                    'message' => $this->translator->trans('bitbag.shopware_poczta_polska_app.order.custom_fields.height_invalid'),
                ]),
                new Assert\Length([
                    'min' => 1,
                    'minMessage' => $this->translator->trans('bitbag.shopware_poczta_polska_app.order.custom_fields.height_invalid'),
                ]),
            ],
            $widthKey => [
                new Assert\NotBlank([
                    'message' => $this->translator->trans('bitbag.shopware_poczta_polska_app.order.custom_fields.width_invalid'),
                ]),
                new Assert\Length([
                    'min' => 1,
                    'minMessage' => $this->translator->trans('bitbag.shopware_poczta_polska_app.order.custom_fields.width_invalid'),
                ]),
            ],
            $packageContentsKey => new Assert\NotBlank([
                'message' => $this->translator->trans('bitbag.shopware_poczta_polska_app.order.custom_fields.package_contents_invalid'),
            ]),
            $plannedShippingDateKey => new Assert\NotBlank([
                'message' => $this->translator->trans('bitbag.shopware_poczta_polska_app.order.custom_fields.planned_shipping_date_invalid'),
            ]),
        ]);

        return Validation::createValidator()->validate($data, $constraint);
    }
}
