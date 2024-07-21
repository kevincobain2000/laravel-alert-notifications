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
            'type'        => 'message',
            'attachments' => [
                [
                    'contentType' => 'application/vnd.microsoft.card.adaptive',
                    'contentUrl'  => null,
                    'content'     => [
                        '\$schema'     => 'http://adaptivecards.io/schemas/adaptive-card.json',
                        'type'        => 'AdaptiveCard',
                        'version'     => '1.4',
                        'accentColor' => 'bf0000',
                        'body'        => [
                            [
                                'type'   => 'TextBlock',
                                'text'   => config('laravel_alert_notifications.microsoft_teams.cardSubject'),
                                'id'     => 'title',
                                'size'   => 'large',
                                'weight' => 'bolder',
                                'color'  => 'accent',
                            ],
                            [
                                'type'  => 'FactSet',
                                'facts' => [
                                    [
                                        'title' => 'Environment:',
                                        'value' => config('app.env'),
                                    ],
                                    [
                                        'title' => 'Server:',
                                        'value' => Request::server('SERVER_NAME'),
                                    ],
                                    [
                                        'title' => 'Request Url:',
                                        'value' => Request::fullUrl(),
                                    ],
                                    [
                                        'title' => 'Exception:',
                                        'value' => get_class($this->exception),
                                    ],
                                    [
                                        'title' => 'Message:',
                                        'value' => $this->exception->getMessage(),
                                    ],
                                    [
                                        'title' => 'Exception Code:',
                                        'value' => $this->exception->getCode(),
                                    ],
                                    [
                                        'title' => 'In File:',
                                        'value' => $this->exception->getFile() .' on line '.$this->exception->getLine(),
                                    ],
                                ],
                                'id' => 'acFactSet',
                            ],
                            [
                                'type'        => 'CodeBlock',
                                'codeSnippet' => $this->exception->getTraceAsString(),
                                'fontType'    => 'monospace',
                                'wrap'        => true,
                            ],
                        ],
                        'msteams'     => [
                            'width' => 'Full',
                        ],
                    ],
                ],
            ],
        ];
    }
}
