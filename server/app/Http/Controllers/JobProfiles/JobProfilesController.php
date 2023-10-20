<?php

namespace App\Http\Controllers\JobProfiles;

use App\Http\Controllers\JobProfiles\Models\JobProfile;
use App\Http\Controllers\JobProfiles\Requests\CreateJobProfileRequest;
use App\Http\Controllers\JobProfiles\Requests\DeleteJobProfileRequest;
use App\Http\Controllers\JobProfiles\Requests\UpdateJobProfileRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class JobProfilesController extends Controller
{
    const MODULE_NAME = 'JobProfile';
    const JOB_PROFILE_CREATED = 'New JobProfile created successfully';
    const JOB_PROFILE_NOT_CREATED = 'Error in creating a JobProfile';
    const COLLECTION_NAME = 'JobProfiles';
    const JOB_PROFILE_UPDATED = 'JobProfile updated successfully';
    const JOB_PROFILE_NOT_UPDATED = 'Error in updating JobProfile';
    const JOB_PROFILE_DELETED = 'JobProfile deleted successfully';
    const JOB_PROFILE_NOT_DELETED = 'Error in deleting JobProfile';
    const JOB_PROFILE_ALREADY_MARKED = 'JobProfile already marked';
    const JOB_PROFILE_NOT_FOUND = 'JobProfile not found';

    public function __construct(JobProfile $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $JobProfile = $this->model->when(!empty($request->JobProfileID), function ($query) use ($request) {
                return $query->where('jobpr-ID', $request->JobProfileID);
            });
              if ($JobProfile) {
                  $JobProfile = $this->model
                ->when(!empty($request->JobProfileID), function ($query) use ($request) {
                    return $query->where('jobpr-ID', $request->JobProfileID);
                })
            ->orderBy($this->getSortBy(), $this->getSort())
            ->paginate($this->getPerPage());
                  if ($JobProfile->isNotEmpty()) {
                      return $this->created([JobProfilesController::COLLECTION_NAME => $JobProfile]);
                  }
              }
            return $this->noRecord(['message' => JobProfilesController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateJobProfileRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => JobProfilesController::JOB_PROFILE_CREATED]);
            }
            return $this->failed(['message' => JobProfilesController::JOB_PROFILE_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateJobProfileRequest $request)
    {
        try {
            $updatedJobProfile = $this->model::where('id', $request->id)->update($request->all());
            if ($updatedJobProfile) {
                return $this->created(['message' => JobProfilesController::JOB_PROFILE_UPDATED]);
            }
            return $this->failed(['message' => JobProfilesController::JOB_PROFILE_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteJobProfileRequest $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => JobProfilesController::JOB_PROFILE_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    }

