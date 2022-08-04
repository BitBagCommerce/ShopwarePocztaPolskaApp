<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Factory;

use BitBag\ShopwareAppSystemBundle\Model\Feedback\Notification\Error;
use BitBag\ShopwareAppSystemBundle\Model\Feedback\Notification\Success;
use BitBag\ShopwareAppSystemBundle\Model\Feedback\Notification\Warning;
use BitBag\ShopwareAppSystemBundle\Response\FeedbackResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

final class FeedbackResponseFactory implements FeedbackResponseFactoryInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function createError(string $messageKey): JsonResponse
    {
        return new FeedbackResponse(new Error($this->translator->trans($messageKey)));
    }

    public function createSuccess(string $messageKey): JsonResponse
    {
        return new FeedbackResponse(new Success($this->translator->trans($messageKey)));
    }

    public function createWarning(string $messageKey): JsonResponse
    {
        return new FeedbackResponse(new Warning($this->translator->trans($messageKey)));
    }
}
