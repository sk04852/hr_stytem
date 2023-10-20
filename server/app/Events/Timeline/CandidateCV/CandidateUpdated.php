<?php

namespace App\Events\Timeline\CandidateCV;

use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CandidateUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $candidatecv;

    public $event_name;

    public $old_values;

    public $new_values;

    public $data;

    public $additional_information;

    /**
     * @param CandidateCV $candidatecv
     * @param string $event_name
     * @param $old_values
     * @param $new_values
     * @param $data
     * @param $additional_information
     */
    public function __construct(CandidateCV $candidatecv, string $event_name = 'Candidate Updated', $old_values = null, $new_values = null, $data = null, $additional_information = null)
    {
        $this->candidatecv = $candidatecv;
        $this->event_name = $event_name;
        $this->old_values = $old_values;
        $this->new_values = $new_values;
        $this->data = $data;
        $this->additional_information = $additional_information;
    }
}
