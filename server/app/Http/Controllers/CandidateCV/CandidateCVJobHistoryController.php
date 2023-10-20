<?php

namespace App\Http\Controllers\CandidateCV;

use App\Events\Timeline\CandidateCV\CandidateJobHistoryCreated;
use App\Events\Timeline\CandidateCV\CandidateJobHistoryDeleted;
use App\Events\Timeline\CandidateCV\CandidateJobHistoryUpdated;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\CandidateCV\Models\CandidateCVJobHistory;
use App\Http\Controllers\CandidateCV\Requests\CreateJobHistoryRequest;
use App\Http\Controllers\CandidateCV\Requests\DeleteJobHistoryRequest;
use App\Http\Controllers\CandidateCV\Requests\UpdateJobHistoryRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Tags\Models\Tag;
use App\Http\Controllers\Timelines\Models\Timeline;
use App\Http\Controllers\Timelines\Traits\TimelineTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CandidateCVJobHistoryController extends Controller
{
    use TimelineTrait;

    const MODULE_NAME = 'CandidateCV';
    const JOB_HISTORY_CREATED = 'New job history created successfully';
    const JOB_HISTORY_NOT_CREATED = 'Error in creating a Job History';
    const COLLECTION_NAME = 'CandidateCVJobHistories';
    const JOB_HISTORY_UPDATED = 'Job History updated successfully';
    const JOB_HISTORY_NOT_UPDATED = 'Error in updating Job History';
    const JOB_HISTORY_DELETED = 'Job History deleted successfully';
    const JOB_HISTORY_NOT_DELETED = 'Error in deleting Job History';
    const JOB_HISTORY_ALREADY_MARKED = 'Job History already marked';
    const JOB_HISTORY_NOT_FOUND = 'Job History not found';

    public function __construct(CandidateCVJobHistory $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request, $id)
    {
        try {
            $JobHistories = $this->model->when(!empty($request->candidatecv_id), function ($query) use ($id) {
                return $query->where('candidatecv_id', $id);
            });
            if ($JobHistories) {
                $JobHistories = $this->model
                    ->orderBy($this->getSortBy(), $this->getSort())
                    ->paginate($this->getPerPage())->where('candidatecv_id', $id);
                if ($JobHistories->isNotEmpty()) {
                    return $this->created([CandidateCVJobHistoryController::COLLECTION_NAME => $JobHistories]);
                }
            }
            return $this->noRecord(['message' => CandidateCVJobHistoryController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateJobHistoryRequest $request)
    {
        try {

            DB::transaction(function () use ($request) {
                $tags = collect([]);
                CandidateCV::disableSearchSyncing();
                $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
                $job_histories = $request->get('jobs');

                $candidatecv = CandidateCV::find($job_histories[0]['candidatecv_id']);
                if(is_null($candidatecv)){
                    throw new ModelNotFoundException('Candidate Not Found');
                }

                if (is_array($job_histories) && !empty($job_histories)) {
                    foreach ($job_histories as $job_history) {
                        $candidatecv_job_history = CandidateCVJobHistory::create($job_history);

                        //Extract keywords from Job Designation and add them to Candidate Tags
                        $tags = $tags->merge(array_keys(extract_keywords($candidatecv_job_history->designation)));
                        
                        if(!$candidatecv_job_history instanceof  CandidateCVJobHistory){
                            throw new \Exception(CandidateCVJobHistoryController::JOB_HISTORY_NOT_CREATED);
                        }
                        $this->recordOneToManyNew('jobHistory', $candidatecv_job_history->designation, $timeline_data);
                    }
                }


                //Start Add tags to candidate and Timeline track
                if ($tags->isNotEmpty()) {
                    $old_tags = $candidatecv->tags;
                    foreach ($tags as $tag) {
                        $temp_tag = Tag::firstOrCreate(['name' => $tag]);
                        if(!$old_tags->contains('id', $temp_tag->id)){
                            $candidatecv->tags()->attach($temp_tag->id);
                            $this->recordOneToManyNew('tags', $temp_tag->name, $timeline_data);
                        }
                    }
                }
                //End Add tags to candidate and Timeline track

                $candidatecv->searchable();
                CandidateJobHistoryCreated::dispatch($candidatecv, 'Job History Added', $timeline_data['old_values'], $timeline_data['new_values']);
            });

            CandidateCV::enableSearchSyncing();
            return $this->created(['message' => CandidateCVJobHistoryController::JOB_HISTORY_CREATED]);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function update(UpdateJobHistoryRequest $request)
    {
        try {
            $tags = collect([]);
            CandidateCV::disableSearchSyncing();
            $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
            $update_job_history = $this->model::find($request->id);
            if(is_null($update_job_history)){
                throw new ModelNotFoundException('Job Record not found');
            }
            $update_job_history->fill($request->except('id'));
            if($update_job_history->isDirty()){
                //Extract keywords from Job Designation and add them to Candidate Tags
                $tags = $tags->merge(array_keys(extract_keywords($update_job_history->designation)));

                $this->recordOneToManyUpdate($update_job_history, 'jobHistory', $timeline_data);
            }
            if ($update_job_history->save()) {

                //Start Add tags to candidate and Timeline track
                if ($tags->isNotEmpty()) {
                    $old_tags = $update_job_history->candidatecv->tags;
                    foreach ($tags as $tag) {
                        $temp_tag = Tag::firstOrCreate(['name' => $tag]);
                        if(!$old_tags->contains('id', $temp_tag->id)){
                            $update_job_history->candidatecv->tags()->attach($temp_tag->id);
                            $this->recordOneToManyNew('tags', $temp_tag->name, $timeline_data);
                        }
                    }
                }
                //End Add tags to candidate and Timeline track

                $update_job_history->candidatecv->searchable();
                CandidateJobHistoryUpdated::dispatch($update_job_history->candidatecv, 'Job History Updated', $timeline_data['old_values'], $timeline_data['new_values']);
                CandidateCV::enableSearchSyncing();
                return $this->created([
                    'message' => CandidateCVJobHistoryController::JOB_HISTORY_UPDATED,
                    'data' => $this->model->toArray()
                ]);
            }
            CandidateCV::enableSearchSyncing();
            return $this->failed(['message' => CandidateCVJobHistoryController::JOB_HISTORY_NOT_UPDATED]);
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteJobHistoryRequest $request)
    {
        try {
            $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
            $job_history = $this->model::find($request->id);
            $candidatecv= $job_history->candidatecv;
            $this->recordOneToManyDelete($job_history, 'jobHistory', 'designation', $timeline_data);
            if($job_history->delete()){
                $candidatecv->searchable();
                CandidateJobHistoryDeleted::dispatch($candidatecv, 'Job History Deleted', $timeline_data['old_values'], $timeline_data['new_values']);
                CandidateCV::enableSearchSyncing();
                return $this->created(['message' => CandidateCVJobHistoryController::JOB_HISTORY_DELETED]);
            }

            CandidateCV::enableSearchSyncing();
            return $this->failed(['message' => CandidateCVJobHistoryController::JOB_HISTORY_NOT_DELETED]);
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }
}

