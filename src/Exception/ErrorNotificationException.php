<?php

declare(strict_types=1);

namespace BitBag\ShopwarePocztaPolskaApp\Exception;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

final class ErrorNotificationException extends BadRequestException
{
}
