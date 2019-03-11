<?php
namespace Kevincobain2000\LaravelAlertNotifications\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExceptionOccurredMail extends Mailable
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $view = 'vendor.laravel_alert_notifications.mail';

    public function __construct($exception)
    {
        $this->exception = $exception;
    }

    public function build()
    {
        $subject = config('laravel_alert_notifications.mail.mailSubject');

        $from = config('laravel_alert_notifications.mail.fromAddress');
        $to = config('laravel_alert_notifications.mail.toAddress');

        $data = [
            'exception' => $this->exception
        ];

        return $this->subject($subject)->from($from)->to($to)->with($data);
    }
}
