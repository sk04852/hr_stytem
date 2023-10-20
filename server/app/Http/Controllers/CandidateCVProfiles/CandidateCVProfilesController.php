<?php

namespace App\Http\Controllers\CandidateCVProfiles;

use App\Http\Controllers\CandidateCVProfiles\Models\CandidateCVProfile;
use App\Http\Controllers\CandidateCVProfiles\Requests\CreateCandidateCVProfileRequest;
use App\Http\Controllers\CandidateCVProfiles\Requests\DeleteCandidateCVProfileRequest;
use App\Http\Controllers\CandidateCVProfiles\Requests\UpdateCandidateCVProfileRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class CandidateCVProfilesController extends Controller
{
    const MODULE_NAME = 'CandidateCVProfiles';
    const CANDIDATE_CV_PROFILE_CREATED = 'New CandidateCVProfiles created successfully';
    const CANDIDATE_CV_PROFILE_NOT_CREATED = 'Error in creating a CandidateCVProfiles';
    const COLLECTION_NAME = 'CandidateCVProfiless';
    const CANDIDATE_CV_PROFILE_UPDATED = 'CandidateCVProfiles updated successfully';
    const CANDIDATE_CV_PROFILE_NOT_UPDATED = 'Error in updating CandidateCVProfiles';
    const CANDIDATE_CV_PROFILE_DELETED = 'CandidateCVProfiles deleted successfully';
    const CANDIDATE_CV_PROFILE_NOT_DELETED = 'Error in deleting CandidateCVProfiles';
    const CANDIDATE_CV_PROFILE_ALREADY_MARKED = 'CandidateCVProfiles already marked';
    const CANDIDATE_CV_PROFILE_NOT_FOUND = 'CandidateCVProfiles not found';

    public function __construct(CandidateCVProfile $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $CandidateCVProfiles = $this->model->when(!empty($request->CVID), function ($query) use ($request) {
                return $query->where('candiadte-ID', $request->CVID);
            });
              if ($CandidateCVProfiles) {
                  $CandidateCVProfiles = $this->model
                  ->orderBy($this->getSortBy(), $this->getSort())
                  ->paginate($this->getPerPage());
                  if ($CandidateCVProfiles->isNotEmpty()) {
                      return $this->created([CandidateCVProfilesController::COLLECTION_NAME => $CandidateCVProfiles]);
                  }
              }
            return $this->noRecord(['message' => CandidateCVProfilesController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateCandidateCVProfileRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => CandidateCVProfilesController::CANDIDATE_CV_PROFILE_CREATED]);
            }
            return $this->failed(['message' => CandidateCVProfilesController::CANDIDATE_CV_PROFILE_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateCandidateCVProfileRequest $request)
    {
        try {
            $updatedCandidateCVProfiles = $this->model::where('id', $request->id)->update($request->all());
            if ($updatedCandidateCVProfiles) {
                return $this->created(['message' => CandidateCVProfilesController::CANDIDATE_CV_PROFILE_UPDATED]);
            }
            return $this->failed(['message' => CandidateCVProfilesController::CANDIDATE_CV_PROFILE_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteCandidateCVProfileRequest $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => CandidateCVProfilesController::CANDIDATE_CV_PROFILE_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    }

