<?php

namespace Kevincobain2000\LaravelAlertNotifications\Exceptions;

use Exception;
use Throwable;

class AlertDispatchMethodFailedException extends Exception
{
    public function __construct($message = "Alert  dispatch method failed", $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function methodFailed(string $method, Throwable $exception): self
    {
        return new static("[$method] Dispatch failed with exception: {$exception->getMessage()}", 0, $exception);
    }
}
