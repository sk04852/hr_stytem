<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoFactorAuthEnabledEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $code;
    public $enableEmail;
    public $verifyOTP;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $code = null, $enableEmail = null ,$verifyOTP = null)
    {
        $this->user = $user;
        $this->code = $code;
        $this->enableEmail = $enableEmail;
        $this->verifyOTP = $verifyOTP;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {


        if ($this->enableEmail == 1) {
            return $this->markdown('emails.enable-email-2fa-request');
        } elseif ($this->verifyOTP == 1) {
            return $this->markdown('emails.verify-otp');
        }

        if ($this->user->type_of_authentication == 'email') {
            return $this->markdown('emails.two-factor-auth-enable');
        }
        return $this->markdown('emails.google-two-factor-auth-enable');

    }
}
