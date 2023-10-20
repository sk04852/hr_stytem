<?php

namespace App\Http\Controllers\Jobs;

use App\Http\Controllers\Jobs\Models\Job;
use App\Http\Controllers\Jobs\Requests\CreateJobRequest;
use App\Http\Controllers\Jobs\Requests\DeleteJobRequest;
use App\Http\Controllers\Jobs\Requests\UpdateJobRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

class JobsController extends Controller
{
    const MODULE_NAME = 'Job';
    const JOB_CREATED = 'New Job created successfully';
    const JOB_NOT_CREATED = 'Error in creating a Job';
    const COLLECTION_NAME = 'Jobs';
    const JOB_UPDATED = 'Job updated successfully';
    const JOB_NOT_UPDATED = 'Error in updating Job';
    const JOB_DELETED = 'Job deleted successfully';
    const JOB_NOT_DELETED = 'Error in deleting Job';
    const JOB_ALREADY_MARKED = 'Job already marked';
    const JOB_NOT_FOUND = 'Job not found';

    public function __construct(Job $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $Job = $this->model->when(!empty($request->Job), function ($query) use ($request) {
                return $query->where('Job-ID', $request->Job);
            });
              if ($Job) {
                  $Job = $this->model
                ->when(!empty($request->Job), function ($query) use ($request) {
                    return $query->where('Job-ID', $request->Job);
                })
            ->orderBy($this->getSortBy(), $this->getSort())
            ->paginate($this->getPerPage());
                  if ($Job->isNotEmpty()) {
                      return $this->created([JobsController::COLLECTION_NAME => $Job]);
                  }
              }
            return $this->noRecord(['message' => JobsController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }

    public function getJobsCandidates(Request $request)
    {
        try {
            $data = DB::table('timeline')->where('job_ID', $request->id)->join('candidatecv', 'candidatecv.id' ,'=', 'timeline.candidate_ID')->select('candidatecv.email', 'timeline.id', 'timeline.action_ID', 'timeline.candidate_ID', 'timeline.userpr_ID', 'timeline.action_name', 'timeline.date_added', 'timeline.created_at', 'timeline.updated_at', 'timeline.job_ID', 'timeline.action_name', 'timeline.comments', 'candidatecv.first_name', 'candidatecv.last_name', 'candidatecv.gender', 'candidatecv.location')->latest("updated_at")->groupBy('id')->get();            
            return response()->json($data);
            return $this->noRecord(['message' => JobsController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateJobRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => JobsController::JOB_CREATED]);
            }
            return $this->failed(['message' => JobsController::JOB_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateJobRequest $request)
    {
        try {
            $updatedJob = $this->model::find($request->id);
            $updatedJob->update($request->all());
            if ($updatedJob) {
                return $this->created(['message' => JobsController::JOB_UPDATED]);
            }else{
                return $this->failed(['message' => JobsController::JOB_NOT_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteJobRequest $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => JobsController::JOB_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    }

