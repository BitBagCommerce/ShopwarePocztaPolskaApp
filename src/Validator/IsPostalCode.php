<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Validator;

use Symfony\Component\Validator\Constraint;

final class IsPostalCode extends Constraint
{
    public string $message = '';
}
