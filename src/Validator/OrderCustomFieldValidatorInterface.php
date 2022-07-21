<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Validator;

interface OrderCustomFieldValidatorInterface
{
    public function validate(array $data): void;
}
