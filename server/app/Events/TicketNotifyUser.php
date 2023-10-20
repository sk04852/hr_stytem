<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\Tickets\Models\Ticket;

class TicketNotifyUser
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $user;
    public $params;

    public function __construct($ticket, $user, $params)
    {
        $this->ticket = $ticket;
        $this->user = $user;
        $this->params = $params;
    }
}
