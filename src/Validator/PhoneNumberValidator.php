<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PhoneNumberValidator implements PhoneNumberValidatorInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function validate(?string $phoneNumber): ConstraintViolationListInterface
    {
        $phoneNumber = str_replace(['+48', '+', '-', ' '], '', (string) $phoneNumber);

        $constraint = new Assert\Collection([
            'phoneNumber' => [
                new Assert\NotBlank([
                    'message' => $this->translator->trans('bitbag.shopware_poczta_polska_app.order.address.phone_number_empty'),
                ]),
                new Assert\Length([
                    'min' => self::PHONE_NUMBER_LENGTH,
                    'max' => self::PHONE_NUMBER_LENGTH,
                    'minMessage' => $this->translator->trans('bitbag.shopware_poczta_polska_app.order.address.phone_number_invalid'),
                    'maxMessage' => $this->translator->trans('bitbag.shopware_poczta_polska_app.order.address.phone_number_invalid'),
                ]),
                new Assert\Regex(
                    self::PHONE_NUMBER_REGEX,
                    $this->translator->trans('bitbag.shopware_poczta_polska_app.order.address.phone_number_invalid')
                ),
            ],
        ]);

        return Validation::createValidator()->validate(['phoneNumber' => $phoneNumber], $constraint);
    }
}
