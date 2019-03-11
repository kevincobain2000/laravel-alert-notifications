<?php

namespace Kevincobain2000\LaravelAlertNotifications;

use Illuminate\Support\ServiceProvider;

class AlertNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'laravel_alert_notifications');

        $this->publishes([
            __DIR__.'/config/laravel_alert_notifications.php' => config_path('laravel_alert_notifications.php'),
        ], 'config');
        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/laravel_alert_notifications'),
        ], 'views');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/laravel_alert_notifications.php',
            'laravel_alert_notifications'
        );
    }
}
