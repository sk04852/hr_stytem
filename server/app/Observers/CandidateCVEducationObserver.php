<?php

namespace App\Observers;

use App\Traits\TimelineTrait;
use App\Http\Controllers\CandidateCV\Models\CandidateCVEducation;
use App\Http\Controllers\EducationDegrees\Models\EducationDegree;
use App\Http\Controllers\Users\Models\User;
use App\Models\History;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CandidateCVEducationObserver
{
    use TimelineTrait;
    /**
     * Handle the CandidateCVEducation "created" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVEducation  $candidateCVEducation
     * @return void
     */
    public function created(CandidateCVEducation $candidateCVEducation)
    {
        $name = $candidateCVEducation->institute;
        History::create([
            'reference_table' => 'candidatecv_education',
            'reference_id'    => $candidateCVEducation->candidatecv_id,
            'actor_id'        => auth()->user()->id,
            'body'            => "Education for ${name} added",
        ]);
    }

    /**
     * Handle the CandidateCVEducation "updated" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVEducation  $candidateCVEducation
     * @return void
     */
    public function updated(CandidateCVEducation $candidateCVEducation)
    {
        $this->track($candidateCVEducation, function ($value, $field) use ($candidateCVEducation) {
            if($field==='Degree id'){
                $field = 'degree name';
                $value = EducationDegree::where('id',$value)->first()->name;
                $old = EducationDegree::where('id',$candidateCVEducation->getOriginal('degree_id'))->first()->name;
                return[
                    'body' => "Education - {$field} changed from {$old} to {$value}",
                ];
            }

            $old = $candidateCVEducation->getOriginal(str_replace(' ', '_', $field));
            $field = Str::ucfirst($field);
            return[
                'body' => "Education - {$field} changed from {$old} to {$value}",
            ];
        }, null, $candidateCVEducation->candidatecv_id);
    }

    /**
     * Handle the CandidateCVEducation "deleted" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVEducation  $candidateCVEducation
     * @return void
     */
    public function deleted(CandidateCVEducation $candidateCVEducation)
    {
        //
    }

    /**
     * Handle the CandidateCVEducation "restored" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVEducation  $candidateCVEducation
     * @return void
     */
    public function restored(CandidateCVEducation $candidateCVEducation)
    {
        //
    }

    /**
     * Handle the CandidateCVEducation "force deleted" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVEducation  $candidateCVEducation
     * @return void
     */
    public function forceDeleted(CandidateCVEducation $candidateCVEducation)
    {
        //
    }
}
