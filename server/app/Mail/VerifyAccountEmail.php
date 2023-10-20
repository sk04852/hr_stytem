<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyAccountEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->view('email.verify_account')->with($this->data);
    }
}
