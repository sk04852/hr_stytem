<?php

namespace App\Events\Timeline\CandidateCV;

use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CandidateProfileConfirmed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $candidatecv;

    public $event_name;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CandidateCV $candidatecv, string $event_name = 'Profile Confirmed by Candidate')
    {
        $this->candidatecv = $candidatecv;
        $this->event_name = $event_name;
    }
}
