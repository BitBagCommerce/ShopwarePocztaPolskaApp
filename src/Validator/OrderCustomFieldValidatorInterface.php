<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Validator;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface OrderCustomFieldValidatorInterface
{
    public function validate(array $data): ConstraintViolationListInterface;
}
