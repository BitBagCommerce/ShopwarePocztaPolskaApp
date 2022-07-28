<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Exception\Order;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class OrderDocumentNotFoundException extends NotFoundHttpException
{
}
