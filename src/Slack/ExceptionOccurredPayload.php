<?php

namespace Kevincobain2000\LaravelAlertNotifications\Slack;

use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Request;
use Psr\Log\LogLevel;

class ExceptionOccurredPayload
{
    protected $exception;
    protected $exceptionContext;
    protected $notificationLevel;

    public function __construct($exception, array $exceptionContext = [], string $notificationLevel = LogLevel::ERROR)
    {
        $this->exception         = $exception;
        $this->exceptionContext  = $exceptionContext;
        $this->notificationLevel = $notificationLevel;
    }

    public function getCard()
    {
        $color = match ($this->notificationLevel) {
            LogLevel::EMERGENCY => config('laravel_alert_notifications.slack.notification_color.emergency', '#ff0000'),
            LogLevel::ALERT     => config('laravel_alert_notifications.slack.notification_color.alert', '#dc143c'),
            LogLevel::CRITICAL  => config('laravel_alert_notifications.slack.notification_color.critical', '#ff4500'),
            LogLevel::ERROR     => config('laravel_alert_notifications.slack.notification_color.error', '#b22222'),
            LogLevel::WARNING   => config('laravel_alert_notifications.slack.notification_color.warning', '#ffd700'),
            LogLevel::NOTICE    => config('laravel_alert_notifications.slack.notification_color.notice', '#add8e6'),
            LogLevel::INFO      => config('laravel_alert_notifications.slack.notification_color.info', '#36a64f'),
            LogLevel::DEBUG     => config('laravel_alert_notifications.slack.notification_color.debug', '#a9a9a9'),
            default             => config('laravel_alert_notifications.slack.notification_color.error', '#b22222'),
        };

        return [
            'username'    => config('laravel_alert_notifications.slack.username'),
            'channel'     => config('laravel_alert_notifications.slack.channel'),
            'text'        => config('laravel_alert_notifications.slack.subject'),
            'icon_emoji'  => config('laravel_alert_notifications.slack.emoji'),
            'attachments' => [
                [
                    'color'  => $color,
                    'fields' => [
                        [
                            'title' => 'Environment',
                            'value' => config('app.env'),
                            'short' => true,
                        ],
                        [
                            'title' => 'Severity Level',
                            'value' => strtoupper($this->notificationLevel),
                            'short' => true,
                        ],
                        [
                            'title' => 'Server',
                            'value' => gethostname(),
                            'short' => false,
                        ],
                        [
                            'title' => 'Request Method',
                            'value' => Request::method(),
                            'short' => true,
                        ],
                        [
                            'title' => 'Request URL',
                            'value' => Request::fullUrl(),
                            'short' => true,
                        ],
                        [
                            'title' => 'Exception',
                            'value' => get_class($this->exception),
                            'short' => false,
                        ],
                        [
                            'title' => 'Message',
                            'value' => $this->exception->getMessage(),
                            'short' => true,
                        ],
                        [
                            'title' => 'Exception Code',
                            'value' => $this->exception->getCode(),
                            'short' => true,
                        ],
                        [
                            'title' => 'File',
                            'value' => $this->exception->getFile() . ':' . $this->exception->getLine(),
                            'short' => false,
                        ],
                        [
                            'title' => 'Context',
                            'value' => '```$context = ' . var_export($this->exceptionContext, true) . ';```',
                            'short' => false,
                        ],
                        [
                            'title' => 'Stack Trace',
                            'value' => '```' . $this->exception->getTraceAsString() . '```',
                            'short' => false,
                        ],
                    ],
                ],
            ],
        ];
    }
}
