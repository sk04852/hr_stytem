<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientStatementEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $client;
    public $transactions;
    public $request;

    public function __construct($client, $transactions, $request)
    {
        $this->client = $client;
        $this->transactions = $transactions;
        $this->request = $request;
    }
}
