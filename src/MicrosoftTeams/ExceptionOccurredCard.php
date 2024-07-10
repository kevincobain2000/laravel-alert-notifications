<?php

namespace Kevincobain2000\LaravelAlertNotifications\MicrosoftTeams;

use Illuminate\Support\Facades\Request;

class ExceptionOccurredCard
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
            '@type'    => 'AdaptiveCard',
            '@context' => 'https://adaptivecards.io/schemas/adaptive-card.json',
            'version'  => '1.2',
            'body'     => [
                [
                    'type' => 'TextBlock',
                    'text' => 'Environment: ' . config('app.env'),
                ],
                [
                    'type' => 'TextBlock',
                    'text' => 'Server: ' . Request::server('SERVER_NAME'),
                ],
                [
                    'type' => 'TextBlock',
                    'text' => 'Request Url: ' . Request::fullUrl(),
                ],
                [
                    'type' => 'TextBlock',
                    'text' => 'Exception: ' . get_class($this->exception),
                ],
                [
                    'type' => 'TextBlock',
                    'text' => 'Message: ' . $this->exception->getMessage(),
                ],
                [
                    'type' => 'TextBlock',
                    'text' => 'Exception Code: ' . $this->exception->getCode(),
                ],
                [
                    'type' => 'TextBlock',
                    'text' => 'In File: ' . $this->exception->getFile() . ' on line ' . $this->exception->getLine(),
                ],
                [
                    'type' => 'TextBlock',
                    'text' => 'Stack Trace: ' . $this->exception->getTraceAsString(),
                ],
                [
                    'type' => 'TextBlock',
                    'text' => 'Context: ' . var_export($this->exceptionContext, true),
                ],
            ],
        ];
    }
}
