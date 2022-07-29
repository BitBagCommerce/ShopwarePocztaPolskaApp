<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Validator;

use BitBag\ShopwarePocztaPolskaApp\Entity\ConfigInterface;

interface ConfigValidatorInterface
{
    public function validate(?ConfigInterface $config): void;
}
