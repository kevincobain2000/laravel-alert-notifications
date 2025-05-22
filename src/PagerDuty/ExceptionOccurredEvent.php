<?php

namespace Kevincobain2000\LaravelAlertNotifications\PagerDuty;

use Illuminate\Support\Facades\Request;
use Psr\Log\LogLevel;

class ExceptionOccurredEvent
{
    protected $exception;
    protected $notificationLevel;
    protected $exceptionContext;

    public function __construct($exception, string $notificationLevel, array $exceptionContext = [])
    {
        $this->exception         = $exception;
        $this->notificationLevel = $notificationLevel;
        $this->exceptionContext  = $exceptionContext;
    }

    public function getPayload()
    {
        // Commented out available keys for reference
        return [
            'payload' => [
                'summary'   => get_class($this->exception),
                'timestamp' => now()->toIso8601String(),
                'severity'  => match ($this->notificationLevel) {
                    LogLevel::EMERGENCY => 'critical',
                    LogLevel::ALERT     => 'critical',
                    LogLevel::CRITICAL  => 'critical',
                    LogLevel::ERROR     => 'error',
                    LogLevel::WARNING   => 'warning',
                    LogLevel::NOTICE    => 'info',
                    LogLevel::INFO      => 'info',
                    LogLevel::DEBUG     => 'info',
                    default             => 'error',
                },
                'source'    => Request::server('SERVER_NAME'),
                'component' => config('app.name') ?? null,
                // 'group'     => null,
                // 'class'     => null,
                'custom_details' => array_merge([
                    'environment'       => config('app.env'),
                    'request_method'    => Request::method(),
                    'request_url'       => Request::fullUrl(),
                    'file'              => $this->exception->getFile(),
                    'line'              => $this->exception->getLine(),
                    'exception_code'    => $this->exception->getCode(),
                    'exception_message' => $this->exception->getMessage(),
                    'exception_trace'   => $this->exception->getTraceAsString(),
                    
                ], $this->exceptionContext),
            ],
            'routing_key'  => config('laravel_alert_notifications.pager_duty.integration_key'),
            'event_action' => 'trigger',
            // 'dedup_key'    => null,
            // 'client'       => null,
            // 'client_url'   => null,
            // 'links'        => [],
            // 'images'       => [],
        ];
    }
}
