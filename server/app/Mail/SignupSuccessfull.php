<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SignupSuccessfull extends Mailable
{
    use Queueable, SerializesModels;

  public $first_name;
  public $email_verification_token;
  
    public function __construct($first_name,$email_verification_token)
    {
        $this->first_name=$first_name;
        $this->email_verification_token=$email_verification_token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.signup-successfull');
    }
}
