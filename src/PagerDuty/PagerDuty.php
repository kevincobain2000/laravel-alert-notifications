<?php

namespace Kevincobain2000\LaravelAlertNotifications\PagerDuty;

use Kevincobain2000\LaravelAlertNotifications\Dispatcher\Webhook;

class PagerDuty
{
    public static function send(ExceptionOccurredEvent $exceptionOccurredEvent)
    {
        $url   = config('laravel_alert_notifications.pagerduty.events_v2_endpoint');
        $proxy = config('laravel_alert_notifications.pagerduty.proxy');
        $body  = $exceptionOccurredEvent->getPayload();

        return Webhook::send($url, $body, $proxy);
    }
}
