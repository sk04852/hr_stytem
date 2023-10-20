<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\TimeOff\Models\EmployeeTimeOff;
class TimeOffApprovedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $status;
    public $note;
    public $user;
    public $timeOffRequest;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(EmployeeTimeOff $timeOffRequest, User $user, string $status, string $note)
    {
        $this->status = $status;
        $this->note = $note;
        $this->user = $user;
        $this->timeOffRequest = $timeOffRequest;
    }

}
