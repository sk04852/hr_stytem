<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoFactorAuthDisabledEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $isDisableEmailRequest;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$isDisableEmailRequest = null)
    {
        $this->user = $user;
        $this->isDisableEmailRequest = $isDisableEmailRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->isDisableEmailRequest == 1) {
            return $this->markdown('emails.email-two-factor-auth-disable');
        }
        return $this->markdown('emails.two-factor-auth-disable');
    }
}
