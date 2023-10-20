<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = "Client Invoice Email";

    public $client;
    public $invoice;
    public $products;

    public function __construct($client, $invoice, $products)
    {
        $this->client = $client;
        $this->invoice = $invoice;
        $this->products = $products;
    }
    
    public function build()
    {
        return $this->markdown('emails.invoice-email');
    }
}
