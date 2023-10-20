<?php

namespace App\Events\Timeline\Company;

use App\Http\Controllers\Jobs\Models\JobPr;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyConfirmedJob
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $job_pr;

    public $event_name;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(JobPr $job_pr, string $event_name = 'Job Confirmed by Company')
    {
        $this->job_pr = $job_pr;
        $this->event_name = $event_name;
    }
}
