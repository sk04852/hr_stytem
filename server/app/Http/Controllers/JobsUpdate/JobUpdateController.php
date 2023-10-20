<?php

namespace App\Http\Controllers\JobsUpdate;

use App\Http\Controllers\JobsUpdate\Models\JobUpdate;
use App\Http\Controllers\JobsUpdate\Requests\CreateJobUpdateRequest;
// use App\Http\Controllers\Jobs\Requests\DeleteJobUpdateRequest;
// use App\Http\Controllers\Jobs\Requests\UpdateJobUpdateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class JobUpdateController extends Controller
{
    const MODULE_NAME = 'JobUpdate';
    const JOB__UPDATE__CREATED = 'New JobUpdate created successfully';
    const JOB__UPDATE__NOT__CREATED = 'Error in creating a JobUpdate';
    const COLLECTION_NAME = 'JobUpdate';
    const JOB_UPDATED = 'Job updated successfully';
    const JOB_NOT_UPDATED = 'Error in updating Job';
    const JOB_DELETED = 'Job deleted successfully';
    const JOB_NOT_DELETED = 'Error in deleting Job';
    const JOB_ALREADY_MARKED = 'Job already marked';
    const JOB_NOT_FOUND = 'Job not found';

    public function __construct(JobUpdate $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $JobUpdate = $this->model->when(!empty($request->JobUpdate), function ($query) use ($request) {
                return $query->where('JobUpdate-ID', $request->JobUpdate);
            });
              if ($JobUpdate) {
                  $JobUpdate = $this->model
                ->when(!empty($request->JobUpdate), function ($query) use ($request) {
                    return $query->where('JobUpdate-ID', $request->JobUpdate);
                })
            ->orderBy($this->getSortBy(), $this->getSort())
            ->paginate($this->getPerPage());
                  if ($JobUpdate->isNotEmpty()) {
                      return $this->created([JobUpdateController::COLLECTION_NAME => $JobUpdate]);
                  }
              }
            return $this->noRecord(['message' => JobUpdateController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateJobUpdateRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => JobUpdateController::JOB__UPDATE__CREATED]);
            }
            return $this->failed(['message' => JobUpdateController::JOB__UPDATE__NOT__CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
    // public function update(UpdateJobUpdateRequest $request)
    // {
    //     try {
    //         $updatedJob = $this->model::where('id', $request->id)->update($request->all());
    //         if ($updatedJob) {
    //             return $this->created(['message' => JobUpdateController::JOB_UPDATED]);
    //         }
    //         return $this->failed(['message' => JobUpdateController::JOB_NOT_UPDATED]);
    //     } catch (Exception $ex) {
    //         return $this->serverError($ex);
    //     }
    // }

    // public function destroy(DeleteJobUpdateRequest $request)
    // {
    //     try {
    //         $this->findOneById($request->id)->delete();
    //         return $this->created(['message' => JobUpdateController::JOB_DELETED]);
    //     } catch (Exception $ex) {
    //         return $this->serverError($ex);
    //     }
    // }
    // }

