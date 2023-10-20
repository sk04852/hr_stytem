<?php

namespace App\Events\Timeline\Job;

use App\Http\Controllers\Jobs\Models\JobPr;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteJobVideo
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $job_pr;

    public $event_name;

    public $deleted_file_name;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(JobPr $job_pr, string $event_name = 'Delete Job Video', $deleted_file_name)
    {
        $this->job_pr = $job_pr;
        $this->event_name = $event_name;
        $this->deleted_file_name = $deleted_file_name;
    }
}
