<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminEmailServerDeactivationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $address = 'tabasam@digibits.xyz';
        $subject = 'Email Server Authentication Failed';
        $name = 'tabasamraza';
         
        return $this->view('emails.test')
                    ->from($address, $name)
                    ->cc($address, $name)
                    ->bcc($address, $name)
                    ->replyTo($address, $name)
                    ->subject($subject)
                    ->with([ 'test_message' => $this->data['message'], 'error' => $this->data['error']]);
    }
}