<?php
namespace Kevincobain2000\LaravelAlertNotifications\MicrosoftTeams;

use Kevincobain2000\LaravelAlertNotifications\MicrosoftTeams\ExceptionOccurredCard;
use Kevincobain2000\LaravelAlertNotifications\Dispatcher\Webhook;

class Teams
{
    public static function send(ExceptionOccurredCard $exceptionOccurredCard)
    {
        $url   = config('laravel_alert_notifications.microsoft_teams.webhook');
        $proxy = config('laravel_alert_notifications.microsoft_teams.proxy');
        $body  = $exceptionOccurredCard->getCard();

        return Webhook::send($url, $body, $proxy);
    }
}
