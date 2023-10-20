<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SignupSuccessfullEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $first_name;
    public $email;
    public $email_verification_token;

    public function __construct($first_name, $email, $email_verification_token)
    {
        $this->first_name = $first_name;
        $this->email = $email;
        $this->email_verification_token = $email_verification_token;
    }
}
