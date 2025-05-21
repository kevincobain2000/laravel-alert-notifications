<?php

namespace Kevincobain2000\LaravelAlertNotifications\Dispatcher;

use Illuminate\Support\Facades\Mail;
use Kevincobain2000\LaravelAlertNotifications\Mail\ExceptionOccurredMail;
use Kevincobain2000\LaravelAlertNotifications\MicrosoftTeams\ExceptionOccurredCard;
use Kevincobain2000\LaravelAlertNotifications\MicrosoftTeams\Teams;
use Kevincobain2000\LaravelAlertNotifications\PagerDuty\ExceptionOccurredEvent;
use Kevincobain2000\LaravelAlertNotifications\PagerDuty\PagerDuty;
use Kevincobain2000\LaravelAlertNotifications\Slack\ExceptionOccurredPayload;
use Kevincobain2000\LaravelAlertNotifications\Slack\Slack;
use Throwable;

class AlertDispatcher
{
    public $exception;
    public $exceptionContext;
    public $dontAlertExceptions;
    public $notificationLevel;

    public function __construct(
        Throwable $exception,
        array $dontAlertExceptions = [],
        array $notificationLevelsMapping = [],
        array $exceptionContext = []
    ) {
        $this->exception           = $exception;
        $this->exceptionContext    = $exceptionContext;
        $this->dontAlertExceptions = $dontAlertExceptions;
        $this->notificationLevel   = $notificationLevelsMapping[get_class($exception)]
            ?? config('laravel_alert_notifications.default_notification_level');
    }

    public function notify()
    {
        if ($this->shouldAlert()) {
            return $this->dispatch();
        }

        return false;
    }

    protected function shouldAlert()
    {
        $levelsNotToNotify = config('laravel_alert_notifications.exclude_notification_levels') ?? [];
        if ($this->isDonotAlertException() || in_array($this->notificationLevel, $levelsNotToNotify)) {
            return false;
        }

        if (! config('laravel_alert_notifications.throttle_enabled')) {
            return true;
        }

        return ! ThrottleControl::isThrottled($this->exception);
    }

    protected function dispatch()
    {
        if ($this->shouldMail()) {
            Mail::send(new ExceptionOccurredMail($this->exception, $this->notificationLevel, $this->exceptionContext));
        }

        if ($this->shouldMicrosoftTeams()) {
            Teams::send(new ExceptionOccurredCard($this->exception, $this->exceptionContext));
        }

        if ($this->shouldSlack()) {
            Slack::send(new ExceptionOccurredPayload($this->exception, $this->exceptionContext));
        }

        if ($this->shouldPagerDuty()) {
            PagerDuty::send(new ExceptionOccurredEvent($this->exception, $this->notificationLevel, $this->exceptionContext));
        }

        return true;
    }

    protected function isDonotAlertException(): bool
    {
        return in_array(get_class($this->exception), $this->dontAlertExceptions);
    }

    protected function shouldMail(): bool
    {
        return config('laravel_alert_notifications.mail.enabled')
            && config('laravel_alert_notifications.mail.toAddress');
    }

    protected function shouldMicrosoftTeams(): bool
    {
        return config('laravel_alert_notifications.microsoft_teams.enabled')
            && config('laravel_alert_notifications.microsoft_teams.webhook');
    }

    protected function shouldSlack(): bool
    {
        return config('laravel_alert_notifications.slack.enabled')
            && config('laravel_alert_notifications.slack.webhook');
    }

    protected function shouldPagerDuty(): bool
    {
        return config('laravel_alert_notifications.pager_duty.enabled')
            && config('laravel_alert_notifications.pager_duty.integration_key');
    }
}
