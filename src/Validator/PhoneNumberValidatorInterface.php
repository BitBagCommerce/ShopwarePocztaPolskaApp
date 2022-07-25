<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Validator;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface PhoneNumberValidatorInterface
{
    public const PHONE_NUMBER_REGEX = "/(?:(?:\+|00)[0-9]{1,3})?(\d{9,12})/";

    public const PHONE_NUMBER_LENGTH = 9;

    public function validate(?string $phoneNumber): ConstraintViolationListInterface;
}
