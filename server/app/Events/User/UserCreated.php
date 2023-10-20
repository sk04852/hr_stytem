<?php

namespace App\Events\User;

use App\Models\UserPr;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_pr;

    public $raw_password;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UserPr $user_pr, $raw_password)
    {
        $this->user_pr = $user_pr;
        $this->raw_password = $raw_password;
    }
}
