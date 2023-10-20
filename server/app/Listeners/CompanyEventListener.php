<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\Companies\Models\CompanyTimeline;
use App\Events\CompanyUpdate;
use App\Events\CandidateApplyToJob;
use Illuminate\Support\Facades\Auth;

class CompanyEventListener
{

    const UPDATE_COMPANY_ACTION = 'Ettevõtte andmed uuendatud';

    const CANDIDATE_APPLY_ON_JOB = 'Kandidaat kandideeris töökohale';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function handleCompanyUpdate($event)
    {
        CompanyTimeline::create([
            'company_pr_id' => $event->company_pr->id,
            'candidatecv_id' => null,
            'user_pr_id' => Auth::id(),
            'action_name' => CompanyEventListener::UPDATE_COMPANY_ACTION,
            'comments' => $event->message
        ]);
    }

    public function handleCandidateApplyToJob($event){
        CompanyTimeline::create([
            'company_pr_id' => $event->company_id,
            'candidatecv_id' => $event->candidate_id,
            'user_pr_id' => $event->user_pr_id,
            'action_name' => CompanyEventListener::CANDIDATE_APPLY_ON_JOB,
            'comments' => $event->message
        ]);
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function subscribe($events){

        $events->listen(
            CompanyUpdate::class,
            'App\Listeners\CompanyEventListener@handleCompanyUpdate'
        );

        $events->listen(
            CandidateApplyToJob::class,
            'App\Listeners\CompanyEventListener@handleCandidateApplyToJob'
        );

    }
}
