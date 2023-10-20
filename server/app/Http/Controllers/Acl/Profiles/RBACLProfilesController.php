<?php

namespace App\Http\Controllers\Acl\Profiles;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Acl\Profiles\Requests\CreateProfileRequest as CreateRequest;
use App\Http\Controllers\Acl\Profiles\Requests\UpdateProfileRequest as UpdateRequest;
use App\Http\Controllers\Acl\Models\Profile;
use App\Http\Controllers\Modules\Enums\LogAction;
use App\Http\Controllers\Modules\Enums\LogTypeEnum;
use App\Http\Controllers\Modules\Enums\ModuleEnum;
use App\Http\Resources\RBACL\ProfilesCollection;
use App\Http\Services\RBACLService;
use Exception;

class RBACLProfilesController extends Controller
{
    const RECORD_CREATED = 'Profile has been created successfuly';
    const RECORD_UPDATED = 'Profile has been updated successfuly';
    const RECORD_DELETED = 'Profile has been deleted successfuly';
    const RECORD_NOT_FOUND = 'Profile not found';
    const COLLECTION_NAME = 'profiles';
    private $rBACLService_;

    public function __construct(Profile $model, RBACLService $rBACLService)
    {
        $this->rBACLService_ = $rBACLService;
        $this->model = $model;
    }

    public function index()
    {
        try {
            $data = Profile::orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            if ($data->isNotEmpty()) {
                return $this->created([RBACLProfilesController::COLLECTION_NAME => ProfilesCollection::collection($data)]);
            }
            return $this->noRecord(['message' => RBACLProfilesController::RECORD_NOT_FOUND]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $data = $request->all();
            $attributes = $data['attributes'];
            unset($data['attributes']);
            $data['created_by'] = $this->userId();
            $profile = $this->model->fill($data);
            if ($profile->save()) {
                $this->addLog(LogTypeEnum::Info, null, $profile, $profile, LogAction::Created, ModuleEnum::Profiles);
                $this->rBACLService_->syncProfilePermissions($profile, $attributes);
                return $this->created(['message' => RBACLProfilesController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show(Profile $profile)
    {
        try {
            $permissions = $profile->permissions;
            return $this->created(['profile' => $profile]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $data = $request->all();
            $attributes = $data['attributes'];
            unset($data['attributes']);
            $profile = Profile::where('id', $request->id)->first();
            $oldData = $profile->getOriginal();
            if ($profile->update($data)) {
                $this->addLog(LogTypeEnum::Info, null, $oldData, $profile, LogAction::Updated, ModuleEnum::Profiles);
                $this->rBACLService_->syncProfilePermissions($profile, $attributes);
                return $this->created(['message' => RBACLProfilesController::RECORD_UPDATED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try {
            $Profile = Profile::find($id);
            if ($Profile) {
                $Profile->delete();
                $this->addLog(LogTypeEnum::Info, null, $Profile, null, LogAction::Deleted, ModuleEnum::Profiles);
                return $this->created(['message' => RBACLProfilesController::RECORD_DELETED]);
            }
            return $this->noRecord(['message' => RBACLProfilesController::RECORD_NOT_FOUND]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
