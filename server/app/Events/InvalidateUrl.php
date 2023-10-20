<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvalidateUrl
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $auto_generated_url;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($auto_generated_url)
    {
        $this->auto_generated_url = $auto_generated_url;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
