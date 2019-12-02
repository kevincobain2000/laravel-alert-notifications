<?php

namespace Kevincobain2000\LaravelAlertNotifications;

use Illuminate\Support\ServiceProvider;

class AlertNotificationsServiceProvider extends ServiceProvider
{
    const CONFIG_PATH = __DIR__.'/config/laravel_alert_notifications.php';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'laravel_alert_notifications');

        $this->publishes([
            self::CONFIG_PATH => base_path('config/laravel_alert_notifications.php'),
        ], 'config');
        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/laravel_alert_notifications'),
        ], 'views');

        if (app() instanceof \Laravel\Lumen\Application) {
            app()->configure('laravel_alert_notifications');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'laravel_alert_notifications'
        );
    }
}
