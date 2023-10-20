<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReferralJoinedEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->view('emails.referral-joined')->with($this->data);
    }
}
