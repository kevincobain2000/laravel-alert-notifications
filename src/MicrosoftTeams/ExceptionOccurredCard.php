<?php
namespace Kevincobain2000\LaravelAlertNotifications\MicrosoftTeams;

use Illuminate\Support\Facades\Request;

class ExceptionOccurredCard
{
    public function __construct($exception)
    {
        $this->exception = $exception;
    }

    public function getCard()
    {
        return [
            "@type"      => "MessageCard",
            "@context"   => "http://schema.org/extensions",
            "summary"    => config('laravel_alert_notifications.microsoft_teams.cardSubject'),
            "themeColor" => config('laravel_alert_notifications.themeColor'),
            "title"      => config('laravel_alert_notifications.cardSubject'),
            "sections"   => [
                [
                    "activityTitle"     => config('laravel_alert_notifications.microsoft_teams.cardSubject'),
                    "activitySubtitle"  => "Error has occurred on " . config('app.name') . ' - ' . config('app.name'),
                    "activityImage"     => "",
                    "facts" => [
                        [
                            "name"  => "Environment:",
                            "value" => config('app.env')
                        ],
                        [
                            "name"  => "Request Url:",
                            "value" => Request::fullUrl()
                        ],
                        [
                            "name"  => "Exception:",
                            "value" => get_class($this->exception)
                        ],
                        [
                            "name"  => "Message:",
                            "value" => $this->exception->getMessage()
                        ],
                        [
                            "name"  => "Exception Code:",
                            "value" => $this->exception->getCode()
                        ],
                        [
                            "name"  => "In File:",
                            "value" => '<b style="color:red;">'
                                        . $this->exception->getFile() . ' on line ' . $this->exception->getLine() . '</b>'
                        ],
                        [
                            "name"  => "Server:",
                            "value" => "<pre>" . $this->exception->getTraceAsString() . "</pre>"
                        ],
                    ],
                ]
            ]
        ];
    }
}
