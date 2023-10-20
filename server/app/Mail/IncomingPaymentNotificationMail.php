<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IncomingPaymentNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $subject = "Incoming Payment Email";
    public $userInformation;
    public $clientInformation;
    public $paymentInformation;


    public function __construct($userInformation, $clientInformation, $paymentInformation)
    {
        $this->userInformation = $userInformation;
        $this->clientInformation = $clientInformation;
        $this->paymentInformation = $paymentInformation;
    }

    public function build()
    {
        return $this->markdown('emails.payments')
        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
        ->subject("New payment transaction");
    }
}
