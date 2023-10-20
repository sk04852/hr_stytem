<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewClientAddedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $client;
    public $code;

    public function __construct($client, $code)
    {
        $this->client = $client;
        $this->code = $code;
    }
}
