<?php

namespace Kevincobain2000\LaravelAlertNotifications\Dispatcher;

use Illuminate\Support\Facades\Mail;
use Kevincobain2000\LaravelAlertNotifications\Exceptions\AlertDispatchFailedException;
use Kevincobain2000\LaravelAlertNotifications\Exceptions\AlertDispatchMethodFailedException;
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
    public const DISPATCH_METHODS = [
        'mail',
        'microsoftTeams',
        'slack',
        'pagerDuty',
    ];

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

    public function shouldAlert()
    {
        $levelsNotToNotify = config('laravel_alert_notifications.exclude_notification_levels') ?? [];
        if ($this->isDontAlertException() || in_array($this->notificationLevel, $levelsNotToNotify)) {
            return false;
        }

        if (! config('laravel_alert_notifications.throttle_enabled')) {
            return true;
        }

        return ! ThrottleControl::isThrottled($this->exception);
    }

    protected function dispatch()
    {
        // Attempt all notification channels via try/catch blocks to ensure
        // if one fails, the others can still be attempted.
        $exceptions = [];
        foreach (self::DISPATCH_METHODS as $method) {
            try {
                if (! $this->{"should" . ucfirst($method)}()) {
                    continue;
                }

                $this->{"send" . ucfirst($method)}();
            } catch (Throwable $e) {
                // If the dispatch method fails, we call the handler method to handle the exception
                // and then throw a new AlertDispatchMethodFailedException with the original exception.
                $exceptions[] = AlertDispatchMethodFailedException::methodFailed($method, $e);
            }
        }

        // If any exceptions were thrown during the dispatch process, we throw a new
        // AlertDispatchFailedException that contains all the exceptions that were thrown.
        // This allows the user to see all the failures in one place, rather than just
        // the last one that was thrown. This is useful for debugging and understanding
        // what went wrong during the dispatch process.
        if (!empty($exceptions)) {
            throw AlertDispatchFailedException::dispatchFailed($exceptions);
        }

        return true;
    }

    public function isDontAlertException(): bool
    {
        return in_array(get_class($this->exception), $this->dontAlertExceptions);
    }

    public function shouldMail(): bool
    {
        return config('laravel_alert_notifications.mail.enabled')
            && config('laravel_alert_notifications.mail.toAddress');
    }

    protected function sendMail(): void
    {
        Mail::send(new ExceptionOccurredMail($this->exception, $this->notificationLevel, $this->exceptionContext));
    }

    public function shouldMicrosoftTeams(): bool
    {
        return config('laravel_alert_notifications.microsoft_teams.enabled')
            && config('laravel_alert_notifications.microsoft_teams.webhook');
    }

    protected function sendMicrosoftTeams(): void
    {
        Teams::send(new ExceptionOccurredCard($this->exception, $this->exceptionContext));
    }

    public function shouldSlack(): bool
    {
        return config('laravel_alert_notifications.slack.enabled')
            && config('laravel_alert_notifications.slack.webhook');
    }

    protected function sendSlack(): void
    {
        Slack::send(new ExceptionOccurredPayload($this->exception, $this->exceptionContext));
    }

    public function shouldPagerDuty(): bool
    {
        return config('laravel_alert_notifications.pager_duty.enabled')
            && config('laravel_alert_notifications.pager_duty.integration_key');
    }

    protected function sendPagerDuty(): void
    {
        PagerDuty::send(new ExceptionOccurredEvent($this->exception, $this->notificationLevel, $this->exceptionContext));
    }
}
