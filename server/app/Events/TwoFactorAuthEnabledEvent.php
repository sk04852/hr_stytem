<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TwoFactorAuthEnabledEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $code;
    public $enableEmail;
    public $verifyOTP;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $code = null , $enableEmail = null ,$verifyOTP = null)
    {
        $this->user = $user;
        $this->code = $code;
        $this->enableEmail = $enableEmail;
        $this->verifyOTP = $verifyOTP;
    }
}
