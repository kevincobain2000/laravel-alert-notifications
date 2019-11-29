<?php

namespace Kevincobain2000\LaravelAlertNotifications\Slack;

use Illuminate\Support\Facades\Request;

class ExceptionOccurredPayload
{
    protected $exception;
    protected $exceptionContext;

    public function __construct($exception, array $exceptionContext = [])
    {
        $this->exception        = $exception;
        $this->exceptionContext = $exceptionContext;
    }

    public function getCard()
    {
        return [
            'username'    => config('laravel_alert_notifications.slack.username'),
            'channel'     => config('laravel_alert_notifications.slack.channel'),
            'icon_emoji'  => config('laravel_alert_notifications.slack.emoji'),
            'attachments' => [
                [
                    'text' => '*Environment:* '.config('app.env')
                        .' '.config('laravel_alert_notifications.slack.subject')
                        .'\n'.'Server: '.Request::server('SERVER_NAME')
                        .'\n'.'Request Url: '.Request::fullUrl()
                        .'\n'.'Message: '.$this->exception->getMessage()
                        .'\n'.'Exception: '.get_class($this->exception)
                        .'\n'.'Exception Code: '.$this->exception->getCode()
                        .'\n'.'In File: *'.$this->exception->getFile().'* on line '.$this->exception->getLine()
                        .'\n'.'Context: '.'```\$context = '.var_export($this->exceptionContext, true).';```'
                        .'\n'.'Stack Trace: '.'```'.$this->exception->getTraceAsString().'```', 'mrkdwn' => true,
                ],
            ],
        ];
    }
}
