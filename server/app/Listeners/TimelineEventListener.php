<?php

namespace App\Listeners;

use App\Events\Timeline\CandidateCV\CandidateLanguageUpdated;
use App\Events\Timeline\CandidateCV\CandidateAdditionalCourseCreated;
use App\Events\Timeline\CandidateCV\CandidateAdditionalCourseDeleted;
use App\Events\Timeline\CandidateCV\CandidateAdditionalCourseUpdated;
use App\Events\Timeline\CandidateCV\CandidateAgreementCreated;
use App\Events\Timeline\CandidateCV\CandidateAgreementDeleted;
use App\Events\Timeline\CandidateCV\CandidateEducationCreated;
use App\Events\Timeline\CandidateCV\CandidateEducationDeleted;
use App\Events\Timeline\CandidateCV\CandidateEducationUpdated;
use App\Events\Timeline\CandidateCV\CandidateFileCreated;
use App\Events\Timeline\CandidateCV\CandidateFileDeleted;
use App\Events\Timeline\CandidateCV\CandidateJobHistoryCreated;
use App\Events\Timeline\CandidateCV\CandidateJobHistoryDeleted;
use App\Events\Timeline\CandidateCV\CandidateJobHistoryUpdated;
use App\Events\Timeline\CandidateCV\CandidateLanguageCreated;
use App\Events\Timeline\CandidateCV\CandidateLanguageDeleted;
use App\Events\Timeline\CandidateCV\CandidateTimelineActionTypeSaved;
use App\Events\Timeline\CandidateCV\ChangeStatus;
use App\Events\Timeline\CandidateCV\CandidateApplyToJob;
use App\Events\Timeline\CandidateCV\CandidateCreated;
use App\Events\Timeline\CandidateCV\CandidateUpdated;
use App\Events\Timeline\Company\CompanyCreated;
use App\Events\Timeline\Company\CompanyUpdated;
use App\Events\Timeline\Job\DeleteJobVideo;
use App\Events\Timeline\Job\JobDeleted;
use App\Events\Timeline\Job\JobUpdated;
use App\Http\Controllers\Actions\Models\Action;
use App\Http\Controllers\Jobs\Models\Job;
use App\Http\Controllers\Jobs\Models\JobPr;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\Timeline\Job\JobCreated;
use Illuminate\Support\Facades\Auth;
use App\Traits\TimelineTrait;
use App\Events\Timeline\CandidateCV\CandidateProfileConfirmed;
use App\Events\Timeline\Company\CompanyConfirmedJob;

class TimelineEventListener
{
    use TimelineTrait;

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
     * Create Event Listener.
     *
     * @param  object  $event
     * @return void
     */
    public function handleJobCreated($event)
    {
        $job_title = $event->job_pr->jobs->first()->offer_name;
        $event->job_pr->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "$job_title tööpakkumine loodud",
            'action' => $event->event_name,
            'data' => null,
            'additional_information' => null
        ]);

        $event->job_pr->companyPr->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "$job_title tööpakkumine loodud",
            'action' => $event->event_name,
            'data' => null,
            'additional_information' => null
        ]);

    }

    /**
     * Create Event Listener.
     *
     * @param  object  $event
     * @return void
     */
    public function handleJobUpdated($event)
    {
        $job_title = $event->job_pr->jobs->first()->offer_name;
        $event->job_pr->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "$job_title tööpakkumise info uuendatud",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }

    public function handleDeleteJobVideo ($event){
        $job_title = $event->job_pr->jobs->first()->offer_name;

        $event->job_pr->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => $event->deleted_file_name . "on kustutatud $job_title",
            'action' => $event->event_name,
            'data' => null,
            'additional_information' => null
        ]);
    }

    /**
     * Create Event Listener.
     *
     * @param  object  $event
     * @return void
     */
    public function handleCompanyCreated($event)
    {
        $company_name = $event->company_pr->company->first()->name;
        $event->company_pr->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "$company_name ettevõte loodud",
            'action' => $event->event_name,
            'data' => null,
            'additional_information' => null
        ]);

    }

    /**
     * Create Event Listener.
     *
     * @param  object  $event
     * @return void
     */
    public function handleCompanyUpdated($event)
    {
        $company_name = $event->company_pr->company->first()->name;
        $event->company_pr->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "$company_name ettevõte info uuendatud",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }

    /**
     * Create Event Listener.
     *
     * @param  object  $event
     * @return void
     */
    public function handleCandidateCreated($event)
    {
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "$candidate_name kandidaadi profiil loodud",
            'action' => $event->event_name,
            'data' => null,
            'additional_information' => null
        ]);

    }

    /**
     * Create Event Listener.
     *
     * @param  object  $event
     * @return void
     */
    public function handleCandidateUpdated($event)
    {
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "$candidate_name kandidaadi profiili info uuendatud",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }

    /**
     * Create Event Listener.
     *
     * @param  object  $event
     * @return void
     */
    public function handleCandidateApplyToJob($event){
        $job_pr_object = JobPr::find($event->job_pr_id);
        if(is_null($job_pr_object)){
            throw new ModelNotFoundException('Tööpakkumist ei leitud');
        }

        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $job_title = $job_pr_object->jobs->first()->offer_name;

        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Kandideeris $job_title ametikohale",
            'action' => $event->event_name,
            'data' => null,
            'additional_information' => null
        ]);

        $job_pr_object->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "$candidate_name kandideeris ametikohale",
            'action' => $event->event_name,
            'data' => null,
            'additional_information' => null
        ]);

        $job_pr_object->companyPr->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "$candidate_name kandideeris $job_title ametikohale",
            'action' => $event->event_name,
            'data' => null,
            'additional_information' => null
        ]);
    }


    /**
     * Create Event Listener.
     *
     * @param  object  $event
     * @return void
     */
    public function handleChangeStatus($event){
        $job_pr_object = JobPr::find($event->job_pr_id);
        if(is_null($job_pr_object)){
            throw new ModelNotFoundException('Tööpakkumist ei leitud');
        }

        $old_action_object =  Action::find($event->old_action_id);
        $updated_action_object =  Action::find($event->updated_action_id);
        if(is_null($old_action_object) || is_null($updated_action_object) ){
            throw new ModelNotFoundException('Tegevust ei leitud');
        }

        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $job_title = $job_pr_object->jobs->first()->offer_name;

        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Staatus $job_title ametikohal on muutunud $old_action_object->name - $updated_action_object->name",
            'action' => $event->event_name,
            'data' => null,
            'additional_information' => null
        ]);

        $job_pr_object->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "$candidate_name kandidaadi staatus on muutunud $old_action_object->name - $updated_action_object->name.",
            'action' => $event->event_name,
            'data' => null,
            'additional_information' => null
        ]);

        $job_pr_object->companyPr->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "$candidate_name kandidaadi staatus on muutunud $old_action_object->name - $updated_action_object->name $job_title ametikohal",
            'action' => $event->event_name,
            'data' => null,
            'additional_information' => null
        ]);
    }

    public function handleCandidateJobHistoryCreated($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Tööpakkumise ajalugu on lisatud $candidate_name",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }
    public function handleCandidateJobHistoryUpdated($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Tööpakkumise info on uuendatud $candidate_name",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }
    public function handleCandidateJobHistoryDeleted($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Tööpakkumise info on kustutatud $candidate_name",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }

    public function handleCandidateEducationCreated($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Haridus on lisatud $candidate_name",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }
    public function handleCandidateEducationUpdated($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Haridus on muudetud $candidate_name",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }
    public function handleCandidateEducationDeleted($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Haridus on kustutatud $candidate_name",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }


    public function handleCandidateAdditionalCourseCreated($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Täiendkoolitus on lisatud $candidate_name",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }
    public function handleCandidateAdditionalCourseUpdated($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Täiendkoolitus on uuendatud $candidate_name",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }
    public function handleCandidateAdditionalCourseDeleted($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Täiendkoolitus on kustutatud $candidate_name",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }

    public function handleCandidateLanguageCreated($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Keel on lisatud $candidate_name",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }

    public function handleCandidateLanguageUpdated ($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Keel on lisatud $candidate_name",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }

    public function handleCandidateLanguageDeleted($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Keel on kustutatud $candidate_name",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }

    public function handleCandidateFileCreated($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Manus on lisatud $candidate_name",
            'action' => $event->event_name,
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }

    public function handleCandidateFileDeleted($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Manus on kustatud $candidate_name",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }

    public function handleCandidateAgreementCreated($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Leping on lisatud $candidate_name",
            'action' => $event->event_name,
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }

    public function handleCandidateAgreementDeleted($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => "Leping on kustutatud $candidate_name",
            'action' => $event->event_name,
            'old_values' => json_encode($event->old_values),
            'new_values' => json_encode($event->new_values),
            'data' => $event->data,
            'additional_information' => $event->additional_information
        ]);
    }


    public function handleCandidateTimelineActionTypeSaved($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $title = '';
        $title = (is_null($event->candidatecv->timelineActionType->category)) ? "$candidate_name valis tegevuse ". $event->candidatecv->timelineActionType->name : "$candidate_name valis tegevuse ". $event->candidatecv->timelineActionType->name . ' - ' . $event->candidatecv->timelineActionType->category;
        $event->candidatecv->timeline()->create([
            'user_pr_id' => Auth::id(),
            'title' => $title,
            'action' => $event->event_name,
            'old_values' => null,
            'new_values' => null,
            'data' => null,
            'additional_information' => $event->candidatecv->timeline_action_type_comment
        ]);
    }

    public function handleCandidateProfileConfirmed($event){
        $candidate_name = $event->candidatecv->first_name . ' ' . $event->candidatecv->last_name;
        $title = "Kandidaat $candidate_name on kinnitanud oma profiili ja andnud nõusoleku andmete lisamiseks andmebaasi";
        $event->candidatecv->timeline()->create([
            'user_pr_id' => null,
            'title' => $title,
            'action' => $event->event_name,
            'old_values' => null,
            'new_values' => null,
            'data' => null,
            'additional_information' => null
        ]);
    }

    public function handleCompanyConfirmedJob($event){
        $company_name = $event->job_pr->companyPr->company[0]->name;
        $title = "Ettevõte $company_name on kinnitanud tööpakkumise";
        $event->job_pr->timeline()->create([
            'user_pr_id' => null,
            'title' => $title,
            'action' => $event->event_name,
            'old_values' => null,
            'new_values' => null,
            'data' => null,
            'additional_information' => null
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
            JobCreated::class,
            'App\Listeners\TimelineEventListener@handleJobCreated'
        );

        $events->listen(
            JobUpdated::class,
            'App\Listeners\TimelineEventListener@handleJobUpdated'
        );

        $events->listen(
            DeleteJobVideo::class,
            'App\Listeners\TimelineEventListener@handleDeleteJobVideo'
        );

        $events->listen(
            CompanyCreated::class,
            'App\Listeners\TimelineEventListener@handleCompanyCreated'
        );

        $events->listen(
            CompanyUpdated::class,
            'App\Listeners\TimelineEventListener@handleCompanyUpdated'
        );

        $events->listen(
            CandidateCreated::class,
            'App\Listeners\TimelineEventListener@handleCandidateCreated'
        );

        $events->listen(
            CandidateUpdated::class,
            'App\Listeners\TimelineEventListener@handleCandidateUpdated'
        );

        $events->listen(
            CandidateJobHistoryCreated::class,
            'App\Listeners\TimelineEventListener@handleCandidateJobHistoryCreated'
        );

        $events->listen(
            CandidateJobHistoryUpdated::class,
            'App\Listeners\TimelineEventListener@handleCandidateJobHistoryUpdated'
        );

        $events->listen(
            CandidateJobHistoryDeleted::class,
            'App\Listeners\TimelineEventListener@handleCandidateJobHistoryDeleted'
        );

        $events->listen(
            CandidateEducationCreated::class,
            'App\Listeners\TimelineEventListener@handleCandidateEducationCreated'
        );

        $events->listen(
            CandidateEducationUpdated::class,
            'App\Listeners\TimelineEventListener@handleCandidateEducationUpdated'
        );

        $events->listen(
            CandidateEducationDeleted::class,
            'App\Listeners\TimelineEventListener@handleCandidateEducationDeleted'
        );

        $events->listen(
            CandidateAdditionalCourseCreated::class,
            'App\Listeners\TimelineEventListener@handleCandidateAdditionalCourseCreated'
        );

        $events->listen(
            CandidateAdditionalCourseUpdated::class,
            'App\Listeners\TimelineEventListener@handleCandidateAdditionalCourseUpdated'
        );

        $events->listen(
            CandidateAdditionalCourseDeleted::class,
            'App\Listeners\TimelineEventListener@handleCandidateAdditionalCourseDeleted'
        );

        $events->listen(
            CandidateFileCreated::class,
            'App\Listeners\TimelineEventListener@handleCandidateFileCreated'
        );

        $events->listen(
            CandidateFileDeleted::class,
            'App\Listeners\TimelineEventListener@handleCandidateFileDeleted'
        );

        $events->listen(
            CandidateLanguageCreated::class,
            'App\Listeners\TimelineEventListener@handleCandidateLanguageCreated'
        );

        $events->listen(
            CandidateLanguageUpdated::class,
            'App\Listeners\TimelineEventListener@handleCandidateLanguageUpdated'
        );

        $events->listen(
            CandidateLanguageDeleted::class,
            'App\Listeners\TimelineEventListener@handleCandidateLanguageDeleted'
        );

        $events->listen(
            CandidateAgreementCreated::class,
            'App\Listeners\TimelineEventListener@handleCandidateAgreementCreated'
        );

        $events->listen(
            CandidateAgreementDeleted::class,
            'App\Listeners\TimelineEventListener@handleCandidateAgreementDeleted'
        );

        $events->listen(
            CandidateApplyToJob::class,
            'App\Listeners\TimelineEventListener@handleCandidateApplyToJob'
        );

        $events->listen(
            ChangeStatus::class,
            'App\Listeners\TimelineEventListener@handleChangeStatus'
        );

        $events->listen(
            CandidateTimelineActionTypeSaved::class,
            'App\Listeners\TimelineEventListener@handleCandidateTimelineActionTypeSaved'
        );

        $events->listen(
            CandidateProfileConfirmed::class,
            'App\Listeners\TimelineEventListener@handleCandidateProfileConfirmed'
        );

        $events->listen(
            CompanyConfirmedJob::class,
            'App\Listeners\TimelineEventListener@handleCompanyConfirmedJob'
        );

    }
}
