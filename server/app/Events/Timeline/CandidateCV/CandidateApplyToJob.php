<?php

namespace App\Events\Timeline\CandidateCV;

use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CandidateApplyToJob
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $candidatecv;
    public $job_pr_id;
    public $user_pr_id;
    public $event_name;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CandidateCV $candidatecv, int $job_pr_id, int $user_pr_id, string $event_name = "Candidate Apply on Job")
    {
        $this->candidatecv = $candidatecv;
        $this->job_pr_id = $job_pr_id;
        $this->user_pr_id = $user_pr_id;
        $this->event_name = $event_name;
    }
}
