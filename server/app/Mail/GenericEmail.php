<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $data;
    public function __construct($data)
    {
        $this->data = $data->toArray();
    }

    public function build()
    {
        return $this->view('emails.generic-email')
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->with($this->data);
    }
}

