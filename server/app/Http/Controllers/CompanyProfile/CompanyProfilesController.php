<?php

namespace App\Http\Controllers\CompanyProfile;

use App\Http\Controllers\CompanyProfile\Models\CompanyProfile;
use App\Http\Controllers\CompanyProfile\Requests\CreateCompanyProfileRequest;
use App\Http\Controllers\CompanyProfile\Requests\DeleteCompanyProfileRequest;
use App\Http\Controllers\CompanyProfile\Requests\UpdateCompanyProfileRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class CompanyProfilesController extends Controller
{
    const MODULE_NAME = 'CompanyProfile';
    const COMPANY_PROFILE_CREATED = 'New CompanyProfile created successfully';
    const COMPANY_PROFILE_NOT_CREATED = 'Error in creating a CompanyProfile';
    const COLLECTION_NAME = 'CompanyProfiles';
    const COMPANY_PROFILE_UPDATED = 'CompanyProfile updated successfully';
    const COMPANY_PROFILE_NOT_UPDATED = 'Error in updating CompanyProfile';
    const COMPANY_PROFILE_DELETED = 'CompanyProfile deleted successfully';
    const COMPANY_PROFILE_NOT_DELETED = 'Error in deleting CompanyProfile';
    const COMPANY_PROFILE_ALREADY_MARKED = 'CompanyProfile already marked';
    const COMPANY_PROFILE_NOT_FOUND = 'CompanyProfile not found';

    public function __construct(CompanyProfile $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $CompanyProfile = $this->model->when(!empty($request->CVID), function ($query) use ($request) {
                return $query->where('CV-ID', $request->CVID);
            });
              if ($CompanyProfile) {
                  $CompanyProfile = $this->model
                  ->orderBy($this->getSortBy(), $this->getSort())
                  ->paginate($this->getPerPage());
                  if ($CompanyProfile->isNotEmpty()) {
                      return $this->created([CompanyProfilesController::COLLECTION_NAME => $CompanyProfile]);
                  }
              }
            return $this->noRecord(['message' => CompanyProfilesController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateCompanyProfileRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => CompanyProfilesController::COMPANY_PROFILE_CREATED]);
            }
            return $this->failed(['message' => CompanyProfilesController::COMPANY_PROFILE_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateCompanyProfileRequest $request)
    {
        try {
            $updatedCompanyProfile = $this->model::where('id', $request->id)->update($request->all());
            if ($updatedCompanyProfile) {
                return $this->created(['message' => CompanyProfilesController::COMPANY_PROFILE_UPDATED]);
            }
            return $this->failed(['message' => CompanyProfilesController::COMPANY_PROFILE_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteCompanyProfileRequest $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => CompanyProfilesController::COMPANY_PROFILE_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    }

