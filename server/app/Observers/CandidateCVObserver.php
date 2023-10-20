<?php

namespace App\Observers;

use App\Traits\TimelineTrait;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\Actions\Models\Action;
use App\Http\Controllers\Timelines\Models\Timeline;
use App\Http\Controllers\Jobs\Models\Job;
use App\Models\History;
use App\Http\Controllers\Users\Models\User;
use Carbon\Carbon;

class CandidateCVObserver
{
    use TimelineTrait;
    /**
     * Handle the CandidateCV "created" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCV  $candidateCV
     * @return void
     */
    public function created(CandidateCV $candidateCV)
    {
        History::create([
            'reference_table' => 'candidatecv',
            'reference_id'    => $candidateCV->id,
            'actor_id'        => auth()->user()->id,
            'body'            => 'Created Candidate CV',
        ]);
    }

    /**
     * Handle the CandidateCV "updated" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCV  $candidateCV
     * @return void
     */
    public function updated(CandidateCV $candidateCV)
    {
//        $this->track($candidateCV, function ($value, $field) use ($candidateCV) {
//            $old = $candidateCV->getOriginal(str_replace(' ', '_', $field));
//            if($field==='action id' && $value===8){
//                return[
//                    'body' => "Process stopped for current job",
//                ];
//            }
//            else if($field==='action id'){
//                $old = Action::where('id',$old)->first()->name;
//                $value = Action::where('id',$value)->first()->name;
//                $jobid = Timeline::where('candidate_ID', $candidateCV->id)->latest('updated_at')->limit(1)->value('job_ID');
//                $job = Job::where('id', $jobid)->first()->title;
//                return[
//                    'body' => "Moved Candidate to {$value} for {$job}",
//                ];
//            }
//            if($field==='dob'){
//                $field=str_replace('dob', 'date of birth', $field);
//            }
//            return [
//                'body' => "Updated {$field} to ${value} from ${old}",
//            ];
//        });
    }


    /**
     * Handle the CandidateCV "deleted" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCV  $candidateCV
     * @return void
     */
    public function deleted(CandidateCV $candidateCV)
    {
        //
    }

    /**
     * Handle the CandidateCV "restored" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCV  $candidateCV
     * @return void
     */
    public function restored(CandidateCV $candidateCV)
    {
        //
    }

    /**
     * Handle the CandidateCV "force deleted" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCV  $candidateCV
     * @return void
     */
    public function forceDeleted(CandidateCV $candidateCV)
    {
        //
    }
}
