<?php

namespace Kevincobain2000\LaravelAlertNotifications\Exceptions;

use Exception;
use Throwable;

class AlertDispatchFailedException extends Exception
{
    public $exceptions = [];

    public function __construct($message = "Alert dispatch failed", $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function dispatchFailed(array $exceptions = []): self
    {
        $instance = new static("Alert dispatch failed", 0);
        $instance->exceptions = $exceptions;
        $instance->message = "Alert dispatch failed with the following exceptions:\n" . implode("\n", array_map(function ($e) {
            return $e->getMessage();
        }, $exceptions));
        return $instance;
    }
}
