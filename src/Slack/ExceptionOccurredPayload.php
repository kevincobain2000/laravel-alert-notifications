<?php
namespace Kevincobain2000\LaravelAlertNotifications\Slack;

use Illuminate\Support\Facades\Request;

class ExceptionOccurredPayload
{
    public function __construct($exception)
    {
        $this->exception = $exception;
    }

    public function getCard()
    {
        return [
            "username"   => config('laravel_alert_notifications.slack.username'),
            "channel"    => config('laravel_alert_notifications.slack.channel'),
            "icon_emoji" => config('laravel_alert_notifications.slack.emoji'),
            "attachments" => [
                [
                    "text"  => "*Environment:* "   . config('app.env') . " " . config('laravel_alert_notifications.slack.subject')
                       . "\n" . "Request Url: "    . "http://localhost"
                       . "\n" . "Exception: "      . get_class($this->exception)
                       . "\n" . "Exception Code: " . $this->exception->getCode()
                       . "\n" . "In File: *"       . $this->exception->getFile() . '* on line ' . $this->exception->getLine()
                       . "\n" . "Server: "         . "```" . $this->exception->getTraceAsString() . "```"
                    , "mrkdwn"=> true
                ]
            ]
        ];
    }
}
