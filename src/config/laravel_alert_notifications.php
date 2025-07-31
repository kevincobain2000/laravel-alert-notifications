<?php

return [
    'throttle_enabled'            => true,
    'throttle_duration_minutes'   => 5,
    'cache_driver'                => env('ALERT_NOTIFICATION_CACHE_DRIVER', 'file'),
    'cache_prefix'                => env('ALERT_NOTIFICATION_CACHE_PREFIX', 'laravel-alert-notifications'),
    'default_notification_level'  => 'error',
    'exclude_notification_levels' => ['debug', 'info'],
    'mail'                        => [
        'enabled'     => true,
        'toAddress'   => env('ALERT_NOTIFICATION_MAIL_TO_ADDRESS'),
        'fromAddress' => env('ALERT_NOTIFICATION_MAIL_FROM_ADDRESS'),
        'subject'     => '['.env('APP_ENV').'] ['.trim(`hostname`).'] '.env('APP_NAME'),
        'notice'      => [
            'toAddress' => env('ALERT_NOTIFICATION_MAIL_NOTICE_TO_ADDRESS'),
            'subject'   => '['.env('APP_ENV').'] ['.trim(`hostname`).'] '.env('APP_NAME'),
        ],
        'warning' => [
            'toAddress' => env('ALERT_NOTIFICATION_MAIL_WARNING_TO_ADDRESS'),
            'subject'   => '['.env('APP_ENV').'] ['.trim(`hostname`).'] '.env('APP_NAME'),
        ],
        'error' => [
            'toAddress' => env('ALERT_NOTIFICATION_MAIL_ERROR_TO_ADDRESS'),
            'subject'   => '['.env('APP_ENV').'] ['.trim(`hostname`).'] '.env('APP_NAME'),
        ],
        'critical' => [
            'toAddress' => env('ALERT_NOTIFICATION_MAIL_CRITICAL_TO_ADDRESS'),
            'subject'   => '['.env('APP_ENV').'] ['.trim(`hostname`).'] '.env('APP_NAME'),
        ],
        'alert' => [
            'toAddress' => env('ALERT_NOTIFICATION_MAIL_ALERT_TO_ADDRESS'),
            'subject'   => '['.env('APP_ENV').'] ['.trim(`hostname`).'] '.env('APP_NAME'),
        ],
        'emergency' => [
            'toAddress' => env('ALERT_NOTIFICATION_MAIL_EMERGENCY_TO_ADDRESS'),
            'subject'   => '['.env('APP_ENV').'] ['.trim(`hostname`).'] '.env('APP_NAME'),
        ],
    ],
    'microsoft_teams' => [
        'enabled'     => true,
        'proxy'       => env('ALERT_NOTIFICATION_CURL_PROXY', null),
        'themeColor'  => 'ff5864',
        'cardSubject' => '['.env('APP_ENV').'] ['.trim(`hostname`).'] '.env('APP_NAME'),
        'webhook'     => env('ALERT_NOTIFICATION_MICROSOFT_TEAMS_WEBHOOK'),
    ],
    'slack' => [
        'enabled'  => true,
        'proxy'    => env('ALERT_NOTIFICATION_CURL_PROXY', null),
        'subject'  => '['.env('APP_ENV').'] ['.trim(`hostname`).'] '.env('APP_NAME'),
        'username' => '['.env('APP_ENV').'] ['.trim(`hostname`).'] '.env('APP_NAME'),
        'emoji'    => ':slack:',
        'webhook'  => env('ALERT_NOTIFICATION_SLACK_WEBHOOK', null),
        'channel'  => env('ALERT_NOTIFICATION_SLACK_CHANNEL', null),
        'image'    => null,
        'notification_color' => [
            'emergency' => env('ALERT_NOTIFICATION_SLACK_NOTIFICATION_COLOR_EMERGENCY', '#ff0000'),
            'alert'     => env('ALERT_NOTIFICATION_SLACK_NOTIFICATION_COLOR_ALERT', '#dc143c'),
            'critical'  => env('ALERT_NOTIFICATION_SLACK_NOTIFICATION_COLOR_CRITICAL', '#ff4500'),
            'error'     => env('ALERT_NOTIFICATION_SLACK_NOTIFICATION_COLOR_ERROR', '#b22222'),
            'warning'   => env('ALERT_NOTIFICATION_SLACK_NOTIFICATION_COLOR_WARNING', '#ffd700'),
            'notice'    => env('ALERT_NOTIFICATION_SLACK_NOTIFICATION_COLOR_NOTICE', '#add8e6'),
            'info'      => env('ALERT_NOTIFICATION_SLACK_NOTIFICATION_COLOR_INFO', '#36a64f'),
            'debug'     => env('ALERT_NOTIFICATION_SLACK_NOTIFICATION_COLOR_DEBUG', '#a9a9a9'),
        ]
    ],
    'pager_duty' => [
        'enabled'            => true,
        'proxy'              => env('ALERT_NOTIFICATION_CURL_PROXY', null),
        'events_v2_endpoint' => env('ALERT_NOTIFICATION_PAGER_DUTY_EVENTS_V2_ENDPOINT', 'https://events.pagerduty.com/v2/enqueue'),
        'integration_key'    => env('ALERT_NOTIFICATION_PAGER_DUTY_INTEGRATION_KEY', null),
    ],
];
