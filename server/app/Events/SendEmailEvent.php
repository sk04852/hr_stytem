<?php

namespace App\Events;

use App\Http\Controllers\Emails\Models\Email;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendEmailEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $email;
    public function __construct(Email $email)
    {
        $this->email = $email;
    }
}
