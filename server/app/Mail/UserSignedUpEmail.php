<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserSignedUpEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $data;
    public function __construct($data)
    {
        $this->data = $data->toArray();
        $this->data['email_verification_token'] = $data->email_verification_token; // email_verification_token is now added to hidden. Thats why it should be fetched.
    }

    public function build()
    {
        return $this->view('emails.user-signedup')
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->subject(env('SUBJECT_WELCOME_EMAIL'))
                    ->with($this->data);
    }
}
