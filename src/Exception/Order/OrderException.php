<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Exception\Order;

use Symfony\Component\HttpFoundation\Response;

final class OrderException extends \LogicException
{
    public function getErrorCode(): string
    {
        return 'BITBAG_POCZTA_POLSKA_APP__ORDER_EXCEPTION';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
