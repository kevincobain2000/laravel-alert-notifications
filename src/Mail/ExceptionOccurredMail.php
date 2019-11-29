<?php

namespace Kevincobain2000\LaravelAlertNotifications\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExceptionOccurredMail extends Mailable
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $exception;
    public $exceptionContext;

    public $view = 'vendor.laravel-alert-notifications.mail';

    protected $notificationLevel;

    public function __construct($exception, string $notificationLevel, array $exceptionContext = [])
    {
        $this->exception         = $exception;
        $this->exceptionContext  = $exceptionContext;
        $this->notificationLevel = $notificationLevel;
    }

    public function build()
    {
        $configPrefix = 'laravel_alert_notifications.mail.'.$this->notificationLevel.'.';

        $from    = config('laravel_alert_notifications.mail.fromAddress');
        $to      = config($configPrefix.'toAddress') ?? config('laravel_alert_notifications.mail.toAddress');
        $subject = config($configPrefix.'subject') ?? config('laravel_alert_notifications.mail.subject');
        $subject = $this->replaceSubjectPlaceholders($subject);

        $data = [
            'exception' => $this->exception,
            'context'   => $this->exceptionContext,
        ];

        return $this->subject($subject)->from($from)->to($to)->with($data);
    }

    protected function replaceSubjectPlaceholders(string $subject): string
    {
        $subject = str_replace('%ExceptionType%', get_class($this->exception), $subject);
        $subject = str_replace('%ExceptionMessage%', $this->exception->getMessage(), $subject);
        $subject = str_replace('%ExceptionCode%', $this->exception->getCode(), $subject);
        $subject = str_replace('%ExceptionLevel%', ucfirst($this->notificationLevel), $subject);

        return $subject;
    }
}
