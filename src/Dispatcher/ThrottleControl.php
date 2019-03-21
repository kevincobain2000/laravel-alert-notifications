<?php
namespace Kevincobain2000\LaravelAlertNotifications\Dispatcher;

use Exception;
use DateTime;

use Illuminate\Support\Facades\Cache;

class ThrottleControl
{
    // Check if alert is already sent
    public static function isThrottled(Exception $exception): bool
    {
        $driver = config('laravel_alert_notifications.cache_driver');
        $key = self::getThrottleCacheKey($exception);

        if (Cache::store($driver)->has($key)) {
            return true;
        }

        Cache::store($driver)->put($key, true, self::nowAddMinutes());

        return false;
    }

    public static function getThrottleCacheKey(Exception $exception)
    {
        $key = config('laravel_alert_notifications.cache_prefix')
            . get_class($exception)
            . '-'
            . $exception->getCode();
        return $key;
    }

    private static function nowAddMinutes(): DateTime
    {
        $dateTime = new DateTime();
        $minutesToAdd = config('laravel_alert_notifications.throttle_duration_minutes');
        $dateTime->modify("+{$minutesToAdd} minutes");
        return $dateTime;
    }
}
