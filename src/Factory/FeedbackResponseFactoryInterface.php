<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Factory;

use Symfony\Component\HttpFoundation\JsonResponse;

interface FeedbackResponseFactoryInterface
{
    public function createError(string $messageKey): JsonResponse;

    public function createSuccess(string $messageKey): JsonResponse;

    public function createWarning(string $messageKey): JsonResponse;
}
