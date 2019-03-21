<?php
namespace Kevincobain2000\LaravelAlertNotifications\Dispatcher;

use Exception;
use DateTime;

use Illuminate\Support\Facades\Mail;
use Kevincobain2000\LaravelAlertNotifications\Mail\ExceptionOccurredMail;

use Kevincobain2000\LaravelAlertNotifications\MicrosoftTeams\Teams;
use Kevincobain2000\LaravelAlertNotifications\MicrosoftTeams\ExceptionOccurredCard;

use Kevincobain2000\LaravelAlertNotifications\Slack\Slack;
use Kevincobain2000\LaravelAlertNotifications\Slack\ExceptionOccurredPayload;

use Kevincobain2000\LaravelAlertNotifications\Dispatcher\ThrottleControl;

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

        return ! ThrottleControl::isThrottled($this->exception);
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
}
