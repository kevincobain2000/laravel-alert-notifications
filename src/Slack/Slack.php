<?php

namespace Kevincobain2000\LaravelAlertNotifications\Slack;

use Kevincobain2000\LaravelAlertNotifications\Dispatcher\Webhook;

class Slack
{
    public static function send(ExceptionOccurredPayload $exceptionOccurredPayload)
    {
        $url   = config('laravel_alert_notifications.slack.webhook');
        $proxy = config('laravel_alert_notifications.slack.proxy');
        $body  = $exceptionOccurredPayload->getCard();

        return Webhook::send($url, $body, $proxy);
    }
}
