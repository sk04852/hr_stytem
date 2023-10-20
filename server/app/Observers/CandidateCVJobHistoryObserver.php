<?php

namespace App\Observers;

use App\Traits\TimelineTrait;
use App\Http\Controllers\CandidateCV\Models\CandidateCVJobHistory;
use App\Models\History;
use App\Http\Controllers\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;


class CandidateCVJobHistoryObserver
{   
    use TimelineTrait;
    /**
     * Handle the CandidateCVJobHistory "created" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVJobHistory  $candidateCVJobHistory
     * @return void
     */
    public function created(CandidateCVJobHistory $candidateCVJobHistory)
    {
        $name = $candidateCVJobHistory->company_name;
        History::create([
            'reference_table' => 'candidatecv_job_history',
            'reference_id'    => $candidateCVJobHistory->candidatecv_id,
            'actor_id'        => auth()->user()->id,
            'body'            => "Job History for ${name} added",
        ]);
    }

    /**
     * Handle the CandidateCVJobHistory "updated" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVJobHistory  $candidateCVJobHistory
     * @return void
     */
    public function updated(CandidateCVJobHistory $candidateCVJobHistory)
    {
        $this->track($candidateCVJobHistory, function ($value, $field) use ($candidateCVJobHistory) {
            $old = $candidateCVJobHistory->getOriginal(str_replace(' ', '_', $field));
            $job_name = $candidateCVJobHistory->getOriginal('company_name');
            $field = Str::ucfirst($field);
            return[
                'body' => "Job History Updated - {$field} changed from {$old} to {$value} for ${job_name}",
            ];
        }, null, $candidateCVJobHistory->candidatecv_id);
    }

    /**
     * Handle the CandidateCVJobHistory "deleted" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVJobHistory  $candidateCVJobHistory
     * @return void
     */
    public function deleted(CandidateCVJobHistory $candidateCVJobHistory)
    {
        //
    }

    /**
     * Handle the CandidateCVJobHistory "restored" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVJobHistory  $candidateCVJobHistory
     * @return void
     */
    public function restored(CandidateCVJobHistory $candidateCVJobHistory)
    {
        //
    }

    /**
     * Handle the CandidateCVJobHistory "force deleted" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVJobHistory  $candidateCVJobHistory
     * @return void
     */
    public function forceDeleted(CandidateCVJobHistory $candidateCVJobHistory)
    {
        //
    }
}
