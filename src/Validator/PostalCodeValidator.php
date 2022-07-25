<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PostalCodeValidator implements PostalCodeValidatorInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function validate(string $postalCode): ConstraintViolationListInterface
    {
        $constraint = new Assert\Collection([
            'postalCode' => [
                new Assert\Regex(
                    self::POST_CODE_REGEX,
                    $this->translator->trans('bitbag.shopware_poczta_polska_app.order.address.post_code_invalid')
                ),
            ],
        ]);

        return Validation::createValidator()->validate(['postalCode' => $postalCode], $constraint);
    }
}
