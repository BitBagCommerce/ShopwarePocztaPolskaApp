<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Validator;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface PostalCodeValidatorInterface
{
    public const POST_CODE_REGEX = "/^(\d{2})(-\d{3})?$/i";

    public function validate(string $postalCode): ConstraintViolationListInterface;
}
