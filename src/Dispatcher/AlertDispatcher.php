<?php
namespace Kevincobain2000\LaravelAlertNotifications\Dispatcher;

use Exception;
use DateTime;

use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Mail;
use Kevincobain2000\LaravelAlertNotifications\Mail\ExceptionOccurredMail;

use Kevincobain2000\LaravelAlertNotifications\MicrosoftTeams\Teams;
use Kevincobain2000\LaravelAlertNotifications\MicrosoftTeams\ExceptionOccurredCard;

use Kevincobain2000\LaravelAlertNotifications\Slack\Slack;
use Kevincobain2000\LaravelAlertNotifications\Slack\ExceptionOccurredPayload;

class AlertDispatcher
{
    public $exception;
    public $dontAlertExceptions;

    public function __construct(Exception $exception, array $dontAlertExceptions = [])
    {
        $this->exception = $exception;
        $this->dontAlertExceptions = $dontAlertExceptions;
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
        if (!config('laravel_alert_notifications.throttle_enabled')) {
            return true;
        }
        if ($this->isDonotAlertException()) {
            return false;
        }
        return ! $this->isThrottled();
    }

    protected function dispatch()
    {
        if ($this->shouldMail()) {
            Mail::send(new ExceptionOccurredMail($this->exception));
        }

        if ($this->shouldMicrosoftTeams($this->exception)) {
            Teams::send(new ExceptionOccurredCard($this->exception));
        }

        if ($this->shouldSlack($this->exception)) {
            Slack::send(new ExceptionOccurredPayload($this->exception));
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
            && config('laravel_alert_notifications.mail.toAddress')
            && config('laravel_alert_notifications.mail.fromAddress');
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

    // Check if alert is already sent
    protected function isThrottled(): bool
    {
        $driver = config('laravel_alert_notifications.cache_driver');
        $key = $this->getThrottleCacheKey();

        if (Cache::store($driver)->has($key)) {
            return true;
        }

        Cache::store($driver)->put($key, true, $this->nowAddMinutes());

        return false;
    }

    private function nowAddMinutes()
    {
        $dateTime = new DateTime();
        $minutesToAdd = config('laravel_alert_notifications.throttle_duration_minutes');
        $dateTime->modify("+{$minutesToAdd} minutes");
        return $minutesToAdd;
    }

    protected function getThrottleCacheKey()
    {
        $key = config('laravel_alert_notifications.cache_prefix')
            .get_class($this->exception)
            .'-'
            .$this->exception->getCode();
        return $key;
    }
}
