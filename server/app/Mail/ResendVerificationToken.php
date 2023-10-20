<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResendVerificationToken extends Mailable
{
    use Queueable, SerializesModels;

    public $email_verification_token;
    public function __construct($email_verification_token)
    {
        $this->email_verification_token= $email_verification_token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.resend-verification-token');
    }
}
