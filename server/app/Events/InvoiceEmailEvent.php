<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceEmailEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $client;
    public $invoice;
    public $products;

    public function __construct($client, $invoice, $products)
    {
        $this->client = $client;
        $this->invoice = $invoice;
        $this->products = $products;
    }
}
