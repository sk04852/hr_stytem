<?php

namespace App\Mail;

use App\Http\Controllers\Tickets\Models\Ticket;
use App\Http\Controllers\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketNotifyUserEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $user;
    public $params;
    public $subject = 'Ticket';


    public function __construct($ticket, $user, array $params = [])
    {
        $this->ticket = $ticket;
        $this->user = $user;
        $this->params = $params;
    }

    public function build()
    {
        return $this->markdown('emails.ticket-notify-user')->with(['ticket'=> $this->ticket->toArray(), 'user'=> $this->user->toArray(), 'params'=> $this->params]);
    }
}
