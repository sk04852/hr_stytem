<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ThankYouForPaymentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $subject = "Thank You For Payment Email";
    public $paymentInformation;

    public function __construct($paymentInformation)
    {
        $this->paymentInformation = $paymentInformation;
    }

    public function build()
    {
        return $this->markdown('emails.thank-you-for-payment')
        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
        ->subject("Thank your for your payment");
    }
}
