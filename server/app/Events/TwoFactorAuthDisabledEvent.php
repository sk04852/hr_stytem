<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TwoFactorAuthDisabledEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $isDisableEmailRequest;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user,$isDisableEmailRequest = null)
    {
        $this->user = $user;
        $this->isDisableEmailRequest = $isDisableEmailRequest;
    }
}
