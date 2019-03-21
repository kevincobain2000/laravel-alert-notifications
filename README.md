## Laravel Alert Notifications

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

### Installation Laravel

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


### Publish

```
php artisan vendor:publish --provider="Kevincobain2000\LaravelAlertNotifications\AlertNotificationsServiceProvider"
php aritsan config:cache
```


### Env

```
ALERT_NOTIFICATION_MAIL_FROM_ADDRESS=
ALERT_NOTIFICATION_MAIL_TO_ADDRESS=
ALERT_NOTIFICATION_CACHE_DRIVER=file
ALERT_NOTIFICATION_MICROSOFT_TEAMS_WEBHOOK=https://outlook.office.com/webhook/.........
ALERT_NOTIFICATION_SLACK_WEBHOOK=https://hooks.slack.com/...
ALERT_NOTIFICATION_CURL_PROXY=
```

### Usage

In **app/Exceptions/Handler.php**. It is better to use a try catch to prevent loop

```
use Kevincobain2000\LaravelAlertNotifications\Dispatcher\AlertDispatcher;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];
    public function report(Exception $exception)
    {
        try {
            $alertDispatcher = new AlertDispatcher($exception, $this->dontReport);
            $alertDispatcher->notify();
        } catch (Exception $e) {
            // do nothing
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
|                               | ``laravel-alert-notifications-ExceptionClass-ExceptionCode`` |
| ALERT_NOTIFICATION_CURL_PROXY | If your slack/MS teams require proxy, then set it up accordingly              |
| email.enabled                 | (default true), false will not notify to email                                |
| email.toAddress               | (default null), null will not notify to email                                 |
| email.fromAddress             | (default null), null will not notify to email                                 |
| microsoft_teams.enabled       | (default true), false will not notify to teams                                |
| microsoft_teams.webhook       | (default null), null will not notify to teams                                 |
| slack.enabled                 | (default true), false will not notify to slack                                |
| slack.webhook                 | (default null), null will not notify to slack                                 |


### Samples

#### Email
<img src="https://i.imgur.com//HpyZbaG.png">

#### Teams
<img src="https://i.imgur.com//PNzrWmA.png">

#### Slack
<img src="https://i.imgur.com/jNoZLED.png">

### References

1. https://qiita.com/kidatti/items/8732114ec4d1727844b8
2. https://laravel-news.com/email-on-error-exceptions