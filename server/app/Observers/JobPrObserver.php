<?php

namespace App\Observers;

use App\Http\Controllers\Jobs\Models\JobPr;
use App\Traits\TimelineTrait;
use Illuminate\Support\Facades\DB;
use App\Models\History;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\Companies\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Str;

class JobPrObserver
{
    use TimelineTrait;
    /**
     * Handle the Job "created" event.
     *
     * @param  \App\Http\Controllers\Jobs\Models\JobPr  $job
     * @return void
     */
    public function created(JobPr $job_pr)
    {
        dd($job_pr);
        $job_title = $job_pr->jobs->first()->title;
        $job_pr->timeline()->create([
            'user_pr_id' => auth()->user()->id,
            'title' => "{$job_title} Job created",
            'action' => 'Created',
            'data' => null,
            'additional_information' => null
        ]);
//        History::create([
//            'reference_table' => 'job',
//            'reference_id'    => $job->id,
//            'actor_id'        => auth()->user()->id,
//            'body'            => "{$name} Job created",
//        ]);

//        History::create([
//            'reference_table' => 'companies',
//            'reference_id'    => $job->company_pr_id,
//            'actor_id'        => auth()->user()->id,
//            'body'            => "{$name} Job created",
//        ]);
    }

    /**
     * Handle the Job "updated" event.
     *
     * @param  \App\Http\Controllers\Jobs\Models\Job  $job
     * @return void
     */
    public function updated(Job $job)
    {
//        $this->track($job, function ($value, $field) use ($job) {
//            $old = $job->getOriginal(str_replace(' ', '_', $field));
//            $job_name = $job->getOriginal('title');
//            $field = Str::ucfirst($field);
//            return[
//                'body' => "{$job_name} Updated - {$field} changed from {$old} to {$value}",
//            ];
//        }, null, $job->id);
    }

    /**
     * Handle the Job "deleted" event.
     *
     * @param  \App\Http\Controllers\Jobs\Models\Job  $job
     * @return void
     */
    public function deleted(Job $job)
    {
        //
    }

    /**
     * Handle the Job "restored" event.
     *
     * @param  \App\Http\Controllers\Jobs\Models\Job  $job
     * @return void
     */
    public function restored(Job $job)
    {
        //
    }

    /**
     * Handle the Job "force deleted" event.
     *
     * @param  \App\Http\Controllers\Jobs\Models\Job  $job
     * @return void
     */
    public function forceDeleted(Job $job)
    {
        //
    }
}
