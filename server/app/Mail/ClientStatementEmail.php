<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientStatementEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $transactions;
    public $request;

    public $subject = 'Client Statement Email';

    public function __construct($client, $transactions, $request)
    {
        $this->client = $client;
        $this->transactions = $transactions;
        $this->request = $request;
    }


    public function build()
    {
        return $this->markdown('emails.client-statement');
    }
}
