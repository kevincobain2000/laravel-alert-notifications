<?php

return [
    'throttle_enabled' => true,
    'throttle_duration_minutes' => 5,
    'cache_driver'     => env('ALERT_NOTIFICATION_CACHE_DRIVER', 'file'),
    'cache_prefix'     => env('ALERT_NOTIFICATION_CACHE_PREFIX', 'laravel-alert-notifications'),

    'mail' => [
        'enabled'     => true,
        'toAddress'   => env('ALERT_NOTIFICATION_MAIL_TO_ADDRESS'),
        'fromAddress' => env('ALERT_NOTIFICATION_MAIL_FROM_ADDRESS'),
        'mailSubject' => '['. env('APP_ENV') . '] [' . trim(`hostname`) . '] ' . env('APP_NAME')
    ],
    'microsoft_teams' => [
        'enabled'     => true,
        'proxy'       => env('ALERT_NOTIFICATION_CURL_PROXY', null),
        'themeColor'  => 'ff5864',
        'cardSubject' => '['. env('APP_ENV') . '] [' . trim(`hostname`) . '] ' . env('APP_NAME'),
        'webhook'     => env('ALERT_NOTIFICATION_MICROSOFT_TEAMS_WEBHOOK')
    ],
    'slack' => [
        'enabled'  => true,
        'proxy'    => env('ALERT_NOTIFICATION_CURL_PROXY', null),
        'subject'  => '['. env('APP_ENV') . '] [' . trim(`hostname`) . '] ' . env('APP_NAME'),
        'username' => '['. env('APP_ENV') . '] [' . trim(`hostname`) . '] ' . env('APP_NAME'),
        'emmoji'   => ':slack:',
        'webhook'  => env('ALERT_NOTIFICATION_SLACK_WEBHOOK', null),
        'channel'  => env('ALERT_NOTIFICATION_SLACK_CHANNEL', null),
        'image'    => null,
    ],
    'pager_duty' => [
        'enabled' => true,
    ],
];
