<?php

namespace App\Events\Timeline\Job;

use App\Http\Controllers\Jobs\Models\JobPr;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $job_pr;

    public $event_name;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(JobPr $job_pr, string $event_name = 'Job Deleted')
    {
        $this->job_pr = $job_pr;
        $this->event_name = $event_name;
    }
}
