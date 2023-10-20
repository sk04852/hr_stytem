<?php

namespace App\Mail;

use App\Http\Controllers\Clients\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientSignUpEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $code;

    public $subject = 'Client Signup Email';

    public function __construct(Client $client, $code)
    {
        $this->client = $client;
        $this->code = $code;
    }

    public function build()
    {
        return $this->markdown('emails.client-registeration');
    }
}
