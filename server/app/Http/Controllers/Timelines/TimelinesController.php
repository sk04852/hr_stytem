<?php

namespace App\Http\Controllers\Timelines;

use App\Http\Controllers\Timelines\Models\Timeline;
use App\Http\Controllers\Jobs\Models\Job;
use App\Http\Controllers\Timelines\Models\TimelineComment;
use App\Http\Controllers\Timelines\Requests\CreateTimelineComment;
use App\Http\Controllers\Timelines\Requests\CreateTimelineRequest;
use App\Http\Controllers\Timelines\Requests\DeleteTimelineRequest;
use App\Http\Controllers\Actions\Models\Action;
use App\Http\Controllers\Timelines\Requests\UpdateTimelineRequest;
use App\Http\Controllers\Controller;
use App\Models\History;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class TimelinesController extends Controller
{
    const MODULE_NAME = 'Timeline';
    const TIMELINE_CREATED = 'New Timeline created successfully';
    const COMMENT_UPDATED = 'New Comment Saved';
    const TIMELINE_NOT_CREATED = 'Error in creating a Timeline';
    const COLLECTION_NAME = 'Timelines';
    const TIMELINE_UPDATED = 'Timeline updated successfully';
    const TIMELINE_NOT_UPDATED = 'Error in updating Timeline';
    const TIMELINE_DELETED = 'Timeline deleted successfully';
    const TIMELINE_NOT_DELETED = 'Error in deleting Timeline';
    const TIMELINE_ALREADY_MARKED = 'Timeline already marked';
    const TIMELINE_NOT_FOUND = 'Timeline not found';
    const TIMELINE_COMMENT_IS_SUCCESSFULLY_POSTED = 'Timeline comment is successfully posted';
    const TIMELINE_COMMENT_IS_FAILED_POSTED = 'Timeline comment is failed to post';
    const TIMELINE_COMMENT_IS_ALREADY_POSTED = 'You are not allowed to post more than 1 comment';

    public function __construct(Timeline $model)
    {
        parent::__construct($model);
    }

    public function storeComment(CreateTimelineComment $request)
    {
        try {
            $timeline_object = $this->model::find($request->timeline_id);
            if(is_null($timeline_object)){
                throw new ModelNotFoundException(TimelinesController::TIMELINE_NOT_FOUND);
            }

            $user_previous_comment = $timeline_object->comments()->where('user_pr_id', Auth::id())->where('timeline_id', $request->timeline_id)->get();

            if($user_previous_comment->isEmpty()){
                $response = $timeline_object->comments()->create([
                    'user_pr_id' => Auth::id(),
                    'body' => $request->body
                ]);

                if($response instanceof TimelineComment){
                    return $this->created(['message' => TimelinesController::TIMELINE_COMMENT_IS_SUCCESSFULLY_POSTED]);
                }

                return $this->failed(['message' => TimelinesController::TIMELINE_COMMENT_IS_FAILED_POSTED]);
            } else {
                return $this->failed(['message' => TimelinesController::TIMELINE_COMMENT_IS_ALREADY_POSTED]);
            }

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

//    public function index(Request $request)
//    {
//        try {
//            $Timeline = $this->model->when(!empty($request->TimelineID), function ($query) use ($request) {
//                return $query->where('candidate-ID', $request->TimelineID);
//            });
//            if ($Timeline) {
//                $Timeline = $this->model
//                    ->when(!empty($request->TimelineID), function ($query) use ($request) {
//                        return $query->where('candidate-ID', $request->TimelineID);
//                    })
//                    ->orderBy($this->getSortBy(), $this->getSort())
//                    ->paginate($this->getPerPage());
//                if ($Timeline->isNotEmpty()) {
//                    return $this->created([TimelinesController::COLLECTION_NAME => $Timeline]);
//                }
//            }
//            return $this->noRecord(['message' => TimelinesController::RECORD_NOT_FOUND], 200);
//        } catch (Exception $ex) {
//            return $this->serverError($ex);
//        }
//
//    }
//
//    public function adduser(Request $request)
//    {
//        try {
//            Timeline::create([
//                'action_ID' => $request->action_ID,
//                'candidate_ID' => $request->candidate_ID,
//                'userpr_ID' => $request->userpr_ID,
//                'comments' => $request->comments,
//                'job_ID' => $request->job_ID,
//                'date_added' => Carbon::now(),
//                'updated_at' => Carbon::now(),
//                'action_name' => Action::where('id', $request->action_ID)->value('name'),
//            ]);
//            return $this->created(['message' => TimelinesController::TIMELINE_CREATED]);
//        } catch (Exception $ex) {
//            return $this->failed(['message' => TimelinesController::TIMELINE_NOT_CREATED]);
//        }
//    }
//
//    public function updatecomment(Request $request)
//    {
//        try {
//            $flag = History::where('id', $request->id);
//            if ($flag->value('actor_id') === auth()->user()->id) {
//                $flag->update([
//                    'comments' => $request->comment,
//                    'updated_at' => Carbon::now(),
//                ]);
//                return $this->created(['message' => TimelinesController::COMMENT_UPDATED]);
//            } else {
//                $this->created(['message' => "You are not authorized to perform this action"]);
//            }
//        } catch (Exception $ex) {
//            return $this->serverError($ex);
//        }
//    }
//
//
//    public function getoffers(Request $request)
//    {
//        try {
//            $data = Timeline::where('candidate_ID', $request->id)->join('job', 'job.id', '=', 'timeline.job_ID')->join('users', 'users.user_ID', '=', 'timeline.userpr_ID')->select('timeline.id', 'timeline.action_ID', 'timeline.candidate_ID', 'timeline.userpr_ID', 'timeline.action_name', 'timeline.date_added', 'timeline.created_at', 'timeline.updated_at', 'timeline.job_ID', 'timeline.action_name', 'job.title', 'users.name')->latest("updated_at")->get()->unique('job_ID');;
//            return response()->json($data);
//        } catch (Exception $ex) {
//            return $this->serverError($ex);
//        }
//    }
//
//    public function getCandidateTimeline(Request $request)
//    {
//        try {
//            $data = History::where([['reference_id', $request->id], ['reference_table', 'candidatecv']])->orWhere([['reference_id', $request->id], ['reference_table', 'candidatecv_job_history']])->orWhere([['reference_id', $request->id], ['reference_table', 'candidatecv_education']])->orWhere([['reference_id', $request->id], ['reference_table', 'candidatecv_files']])->orWhere([['reference_id', $request->id], ['reference_table', 'candidatecv_language']])->orWhere([['reference_id', $request->id], ['reference_table', 'candidatecv_skill']])->join('users', 'users.user_ID', '=', 'candidate_history.actor_id')->latest('candidate_history.created_at')->select('candidate_history.id', 'candidate_history.body', 'candidate_history.comments', 'users.name', 'candidate_history.created_at', 'candidate_history.updated_at')->groupBy('candidate_history.id')->get();
//            return response()->json($data);
//        } catch (Exception $ex) {
//            return $this->serverError($ex);
//        }
//    }
//
//    public function getJobsTimeline(Request $request)
//    {
//        try {
//            $data = History::where([['reference_id', $request->id], ['reference_table', 'job']])->join('users', 'users.user_ID', '=', 'candidate_history.actor_id')->latest('candidate_history.created_at')->select('candidate_history.id', 'candidate_history.body', 'candidate_history.comments', 'users.name', 'candidate_history.created_at', 'candidate_history.updated_at')->groupBy('candidate_history.id')->get();
//            return response()->json($data);
//        } catch (Exception $ex) {
//            return $this->serverError($ex);
//        }
//    }
//
//    public function getCompanyTimeline(Request $request)
//    {
//        try {
//            $data = History::where([['reference_id', $request->id], ['reference_table', 'companies']])->orWhere([['reference_id', $request->id], ['reference_table', 'company_attachments']])->orWhere([['reference_id', $request->id], ['reference_table', 'company_contacts']])->orWhere([['reference_id', $request->id], ['reference_table', 'company_locations']])->join('users', 'users.user_ID', '=', 'candidate_history.actor_id')->latest('candidate_history.created_at')->select('candidate_history.id', 'candidate_history.body', 'candidate_history.comments', 'users.name', 'candidate_history.created_at', 'candidate_history.updated_at')->groupBy('candidate_history.id')->get();
//            return response()->json($data);
//        } catch (Exception $ex) {
//            return $this->serverError($ex);
//        }
//    }
//
//
//    public function store(CreateTimelineRequest $request)
//    {
//        try {
//            $this->model->fill($request->all());
//            if ($this->model->save()) {
//                return $this->created(['message' => TimelinesController::TIMELINE_CREATED]);
//            }
//            return $this->failed(['message' => TimelinesController::TIMELINE_NOT_CREATED]);
//        } catch (Exception $ex) {
//            return $this->serverError($ex);
//        }
//    }
//
//    public function update(UpdateTimelineRequest $request)
//    {
//        try {
//            $updatedTimeline = $this->model::where('id', $request->id)->update($request->all());
//            if ($updatedTimeline) {
//                return $this->created(['message' => TimelinesController::TIMELINE_UPDATED]);
//            }
//            return $this->failed(['message' => TimelinesController::TIMELINE_NOT_UPDATED]);
//        } catch (Exception $ex) {
//            return $this->serverError($ex);
//        }
//    }
//
//    public function destroy(DeleteTimelineRequest $request)
//    {
//        try {
//            $this->findOneById($request->id)->delete();
//            return $this->created(['message' => TimelinesController::TIMELINE_DELETED]);
//        } catch (Exception $ex) {
//            return $this->serverError($ex);
//        }
//    }
}

