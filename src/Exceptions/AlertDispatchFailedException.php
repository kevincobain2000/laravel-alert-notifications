<?php

namespace Kevincobain2000\LaravelAlertNotifications\Exceptions;

use Exception;
use Throwable;

class AlertDispatchFailedException extends Exception
{
    public function __construct($message = "Alert dispatch failed", $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function mailFailed(Throwable $exception): self
    {
        return new static("Mail dispatch failed with exception: {$exception->getMessage()}", 100, $exception);
    }

    public static function microsoftTeamsFailed(Throwable $exception): self
    {
        return new static("Microsoft Teams dispatch failed with exception: {$exception->getMessage()}", 200, $exception);
    }

    public static function slackFailed(Throwable $exception): self
    {
        return new static("Slack dispatch failed with exception: {$exception->getMessage()}", 300, $exception);
    }

    public static function pagerDutyFailed(Throwable $exception): self
    {
        return new static("Pager Duty dispatch failed with exception: {$exception->getMessage()}", 400, $exception);
    }
}
