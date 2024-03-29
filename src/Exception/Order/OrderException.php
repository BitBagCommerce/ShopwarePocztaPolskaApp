<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Exception\Order;

use Symfony\Component\HttpFoundation\Response;

final class OrderException extends \Exception
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
