<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UpdateIncomingEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $subject = "Incoming Payment Email";
    public $userInformation;
    public $clientInformation;
    public $paymentInformation;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userInformation, $clientInformation, $paymentInformation)
    {
        $this->userInformation = $userInformation;
        $this->clientInformation = $clientInformation;
        $this->paymentInformation = $paymentInformation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.update-payments');
    }
}
