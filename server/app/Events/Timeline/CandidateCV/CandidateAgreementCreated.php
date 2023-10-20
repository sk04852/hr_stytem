<?php

namespace App\Events\Timeline\CandidateCV;

use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CandidateAgreementCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $candidatecv;

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
    public function __construct(CandidateCV $candidatecv, string $event_name = 'Candidate Agreement Created', $old_values = null, $new_values = null, $data = null, $additional_information = null)
    {
        $this->candidatecv = $candidatecv;
        $this->event_name = $event_name;
        $this->old_values = $old_values;
        $this->new_values = $new_values;
        $this->data = $data;
        $this->additional_information = $additional_information;
    }
}
