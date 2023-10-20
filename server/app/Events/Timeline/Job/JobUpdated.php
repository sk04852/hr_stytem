<?php

namespace App\Events\Timeline\Job;

use App\Http\Controllers\Jobs\Models\JobPr;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $job_pr;

    public $event_name;

    public $old_values;

    public $new_values;

    public $data;

    public $additional_information;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(JobPr $job_pr, string $event_name = 'Job Updated', $old_values = null, $new_values = null, $data = null, $additional_information = null)
    {
        $this->job_pr = $job_pr;
        $this->event_name = $event_name;
        $this->old_values = $old_values;
        $this->new_values = $new_values;
        $this->data = $data;
        $this->additional_information = $additional_information;
    }
}
