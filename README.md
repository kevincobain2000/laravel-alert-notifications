## Laravel Alert Notifications
<!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->
[![All Contributors](https://img.shields.io/badge/all_contributors-4-orange.svg?style=flat-square)](#contributors-)
<!-- ALL-CONTRIBUTORS-BADGE:END -->

<a href="https://travis-ci.org/kevincobain2000/laravel-alert-notifications"><img src="https://travis-ci.org/kevincobain2000/laravel-alert-notifications.svg?branch=master" alt="Travis Build Status"></a>
<a href="https://scrutinizer-ci.com/g/kevincobain2000/laravel-alert-notifications"><img src="https://scrutinizer-ci.com/g/kevincobain2000/laravel-alert-notifications/badges/quality-score.png?b=master" alt="Quality Score"></a>
<a href="https://scrutinizer-ci.com/g/kevincobain2000/laravel-alert-notifications"><img src="https://scrutinizer-ci.com/g/kevincobain2000/laravel-alert-notifications/badges/build.png?b=master" alt="Build Status"></a>
<a href="https://scrutinizer-ci.com/g/kevincobain2000/laravel-alert-notifications"><img src="https://scrutinizer-ci.com/g/kevincobain2000/laravel-alert-notifications/badges/coverage.png?b=master" alt="Coverage Status"></a>

Send php exceptions to email, microsoft teams, slack.
Notifications are throttle enabled so devs don't get a lot of emails from one host (or all hosts if  cache driver is shared)
Please check config for more details on throttling.

| Channels        | Progress              |
| :-------        | :---------            |
| Email           | Supported             |
| Microsoft Teams | Supported             |
| Slack           | Supported             |
| Pager Duty      | Open to pull requests |

### Installation

```
composer require kevincobain2000/laravel-alert-notifications
```

>If you're using Laravel 5.5+ let the package auto discovery make this for you.

```
'providers' => [
    \Kevincobain2000\LaravelAlertNotifications\AlertNotificationsServiceProvider::class
]
```

### Tests

```
composer install
composer run test
```

### Publish (Laravel)

```
php artisan vendor:publish --provider="Kevincobain2000\LaravelAlertNotifications\AlertNotificationsServiceProvider"
php artisan config:cache
```

### Publish (Lumen)

Since Lumen doesn't support auto-discovery, move config and view files to the destination directories manually

```shell
cp vendor/kevincobain2000/laravel-alert-notifications/src/config/laravel_alert_notifications.php config/laravel_alert_notifications.php
mkdir -p "resources/views/vendor/laravel_alert_notifications/" && cp vendor/kevincobain2000/laravel-alert-notifications/src/views/* resources/views/vendor/laravel_alert_notifications/
```

and add the following to bootstrap/app.php:

```php
$app->register(Kevincobain2000\LaravelAlertNotifications\AlertNotificationsServiceProvider::class);
```

### .env

```
ALERT_NOTIFICATION_MAIL_FROM_ADDRESS=
ALERT_NOTIFICATION_MAIL_TO_ADDRESS=
ALERT_NOTIFICATION_MAIL_NOTICE_TO_ADDRESS=
ALERT_NOTIFICATION_MAIL_WARNING_TO_ADDRESS=
ALERT_NOTIFICATION_MAIL_ERROR_TO_ADDRESS=
ALERT_NOTIFICATION_MAIL_CRITICAL_TO_ADDRESS=
ALERT_NOTIFICATION_MAIL_ALERT_TO_ADDRESS=
ALERT_NOTIFICATION_MAIL_EMERGENCY_TO_ADDRESS=
ALERT_NOTIFICATION_CACHE_DRIVER=file
ALERT_NOTIFICATION_MICROSOFT_TEAMS_WEBHOOK=
ALERT_NOTIFICATION_SLACK_WEBHOOK=
ALERT_NOTIFICATION_CURL_PROXY=
```

### Usage
```php
new AlertDispatcher( 
    Exception $e 
    [, array $dontAlertExceptions = []]         // Exceptions that shouldn't trigger notifications
    [, array $notificationLevelsMapping = []]   // [Exception class => Notification level] mapping
    [, array $exceptionContext = []]            // Array of context data
)
```

In **app/Exceptions/Handler.php**. It is better to use a try catch to prevent loop. 

```
use Kevincobain2000\LaravelAlertNotifications\Dispatcher\AlertDispatcher;

class Handler extends ExceptionHandler
{
    private $exceptionLogLevels = [
        DebugLevelException::class => LogLevel::DEBUG,
        WarningLevelException::class => LogLevel::WARNING,
        ErrorLevelException::class => LogLevel::ERROR,
    ];

    protected $dontReport = [
        //
    ];

    public function report(Throwable $exception)
    {
        try {
            $dontReport = array_merge($this->dontReport, $this->internalDontReport);
            $alertDispatcher = new AlertDispatcher($exception, $dontReport, $this->exceptionLogLevels);
            $alertDispatcher->notify();
        } catch (Throwable $e) {
            // log any unexpected exceptions or do nothing
        }
        parent::report($exception);
    }
}
```

### Config

| config/env key                | purpose                                                                       |
| :----------                   | :--------------                                                               |
| throttle_enabled              | (default true)  If false then library will send alerts without any throttling |
| throttle_duration_minutes     | (default 5 mins) If an exception has been notified                            |
|                               | This will next notify after 5 mins when same exception occurs                 |
| cache_prefix                  | This is a prefix for cache key. Your cache key will look like                 |
|                               | ``laravel-alert-notifications-ExceptionClass-ExceptionCode``                  |
| ALERT_NOTIFICATION_CURL_PROXY | If your slack/MS teams require proxy, then set it up accordingly              |
| default_notification_level    | Default notification level                                                    |
| exclude_notification_levels   | Do not send notification if it is of one of the listed level                  |
| mail                          | E-mail config array:                                                          |
| mail.enabled                  | (default true), false will not notify to email                                |
| mail.fromAddress              | (default null), null will not notify to email                                 |
| mail.toAddress                | Default recipient e-mail address                                              |
| mail.subject                  | Default e-mail subject. May contain placeholders replaced afterwards with     |
|                               | correspondent exception data:                                                 |
|                               | ``%ExceptionMessage%`` => ``$e->getMessage()``                                |
|                               | ``%ExceptionCode%``    => ``$e->getCode()``                                   |
|                               | ``%ExceptionType%``    => ``$e->getType()``                                   |
|                               | ``%ExceptionLevel%``   => ``current notification level``                      |
|                               | ex. ``'subject' => 'Exception [%ExceptionType%] has ocurred``'                |                                 |
| mail.#level#                  | Configs for each notification level                                           |
|                               | notification levels refer to those defined in ``\Psr\Log\LogLevel``           | 
| mail.#level#.toAddress        | (default mail.to_address), #level# notification recipient e-mail              |
| mail.#level#.subject          | #level# notification e-mail subject                                           |
| microsoft_teams.enabled       | (default true), false will not notify to teams                                |
| microsoft_teams.webhook       | (default null), null will not notify to teams                                 |
| slack.enabled                 | (default true), false will not notify to slack                                |
| slack.webhook                 | (default null), null will not notify to slack                                 |


### Samples

#### Email

<img src="https://i.imgur.com/MQFNK93.png" alt="Email">

#### Teams

<img src="https://i.imgur.com/zl20RhQ.png" alt="Teams">

#### Slack

<img src="https://i.imgur.com/jNoZLED.png" alt="Slack">

### References

1. https://qiita.com/kidatti/items/8732114ec4d1727844b8
2. https://laravel-news.com/email-on-error-exceptions

## Contributors ✨

Thanks goes to these wonderful people ([emoji key](https://allcontributors.org/docs/en/emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tr>
    <td align="center"><a href="http://www.abrigham.com"><img src="https://avatars0.githubusercontent.com/u/7387512?v=4" width="100px;" alt=""/><br /><sub><b>Aaron Brigham</b></sub></a><br /><a href="https://github.com/kevincobain2000/laravel-alert-notifications/commits?author=abrigham1" title="Tests">⚠️</a> <a href="https://github.com/kevincobain2000/laravel-alert-notifications/commits?author=abrigham1" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/AlexHupe"><img src="https://avatars1.githubusercontent.com/u/6893843?v=4" width="100px;" alt=""/><br /><sub><b>Alexander Hupe</b></sub></a><br /><a href="https://github.com/kevincobain2000/laravel-alert-notifications/pulls?q=is%3Apr+reviewed-by%3AAlexHupe" title="Reviewed Pull Requests">👀</a> <a href="https://github.com/kevincobain2000/laravel-alert-notifications/commits?author=AlexHupe" title="Tests">⚠️</a> <a href="https://github.com/kevincobain2000/laravel-alert-notifications/commits?author=AlexHupe" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/kitloong"><img src="https://avatars2.githubusercontent.com/u/7660346?v=4" width="100px;" alt=""/><br /><sub><b>Kit Loong</b></sub></a><br /><a href="https://github.com/kevincobain2000/laravel-alert-notifications/pulls?q=is%3Apr+reviewed-by%3Akitloong" title="Reviewed Pull Requests">👀</a> <a href="https://github.com/kevincobain2000/laravel-alert-notifications/commits?author=kitloong" title="Tests">⚠️</a> <a href="https://github.com/kevincobain2000/laravel-alert-notifications/commits?author=kitloong" title="Code">💻</a></td>
    <td align="center"><a href="http://www.standingmist.com"><img src="https://avatars1.githubusercontent.com/u/1041215?v=4" width="100px;" alt=""/><br /><sub><b>Andrew Miller</b></sub></a><br /><a href="https://github.com/kevincobain2000/laravel-alert-notifications/pulls?q=is%3Apr+reviewed-by%3Aikari7789" title="Reviewed Pull Requests">👀</a> <a href="https://github.com/kevincobain2000/laravel-alert-notifications/commits?author=ikari7789" title="Tests">⚠️</a> <a href="https://github.com/kevincobain2000/laravel-alert-notifications/commits?author=ikari7789" title="Code">💻</a></td>
  </tr>
</table>

<!-- markdownlint-enable -->
<!-- prettier-ignore-end -->
<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!