<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationEmailByToken extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

 public $first_name;

    public function __construct($first_name)
    {
        $this->first_name=$first_name;
    }

    public function build()
    {
        return $this->markdown('emails.verification-email-by-token');
    }
}
