<?php

namespace App\Http\Controllers\UserProfiles;

use App\Http\Controllers\UserProfiles\Models\UserProfile;
use App\Http\Controllers\UserProfiles\Requests\CreateUserProfileRequest;
use App\Http\Controllers\UserProfiles\Requests\DeleteUserProfileRequest;
use App\Http\Controllers\UserProfiles\Requests\UpdateUserProfileRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class UserProfilesController extends Controller
{
    const MODULE_NAME = 'UserProfile';
    const USER_PROFILE_CREATED = 'New UserProfile created successfully';
    const USER_PROFILE_NOT_CREATED = 'Error in creating a UserProfile';
    const COLLECTION_NAME = 'UserProfiles';
    const USER_PROFILE_UPDATED = 'UserProfile updated successfully';
    const USER_PROFILE_NOT_UPDATED = 'Error in updating UserProfile';
    const USER_PROFILE_DELETED = 'UserProfile deleted successfully';
    const USER_PROFILE_NOT_DELETED = 'Error in deleting UserProfile';
    const USER_PROFILE_ALREADY_MARKED = 'UserProfile already marked';
    const USER_PROFILE_NOT_FOUND = 'UserProfile not found';

    public function __construct(UserProfile $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $UserProfile = $this->model->when(!empty($request->UserProfileID), function ($query) use ($request) {
                return $query->where('userpr_ID', $request->UserProfileID);
            });
              if ($UserProfile) {
                  $UserProfile = $this->model
                ->when(!empty($request->UserProfileID), function ($query) use ($request) {
                    return $query->where('userpr_ID', $request->UserProfileID);
                })
            ->orderBy($this->getSortBy(), $this->getSort())
            ->paginate($this->getPerPage());
                  if ($UserProfile->isNotEmpty()) {
                      return $this->created([UserProfilesController::COLLECTION_NAME => $UserProfile]);
                  }
              }
            return $this->noRecord(['message' => UserProfilesController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateUserProfileRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => UserProfilesController::USER_PROFILE_CREATED]);
            }
            return $this->failed(['message' => UserProfilesController::USER_PROFILE_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateUserProfileRequest $request)
    {
        try {
            $updatedUserProfile = $this->model::where('id', $request->id)->update($request->all());
            if ($updatedUserProfile) {
                return $this->created(['message' => UserProfilesController::USER_PROFILE_UPDATED]);
            }
            return $this->failed(['message' => UserProfilesController::USER_PROFILE_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteUserProfileRequest $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => UserProfilesController::USER_PROFILE_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    }

