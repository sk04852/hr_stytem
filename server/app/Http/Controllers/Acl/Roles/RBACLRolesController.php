<?php

namespace App\Http\Controllers\Acl\Roles;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Acl\Roles\Requests\CreateRoleRequest as CreateRequest;
use App\Http\Controllers\Acl\Roles\Requests\UpdateRoleRequest as UpdateRequest;
use App\Http\Controllers\Acl\Models\Role;
use App\Http\Controllers\Modules\Enums\LogAction;
use App\Http\Controllers\Modules\Enums\LogTypeEnum;
use App\Http\Controllers\Modules\Enums\ModuleEnum;
use App\Http\Resources\RBACL\RolesCollection;
use App\Http\Services\RBACLService;
use Exception;

class RBACLRolesController extends Controller
{
    const RECORD_CREATED = 'Role has been created successfuly';
    const RECORD_UPDATED = 'Role has been updated successfuly';
    const RECORD_DELETED = 'Role has been deleted successfuly';
    const RECORD_NOT_FOUND = 'Role not found';
    const COLLECTION_NAME = 'Roles';
    private $rBACLService_;

    public function __construct(Role $model, RBACLService $rBACLService)
    {
        parent::__construct($model);
        $this->rBACLService_ = $rBACLService;
    }

    public function index()
    {
        try {
            $data = Role::orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            if ($data->isNotEmpty()) {
                return $this->created([RBACLRolesController::COLLECTION_NAME => RolesCollection::collection($data)]);
            }
            return $this->noRecord(['message' => RBACLRolesController::RECORD_NOT_FOUND]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $data = $this->model->getOnlyFillables($request->all());
            $role = $this->model->create($data);
            if ($role) {
                $this->addLog(LogTypeEnum::Info, null, $role, $role, LogAction::Created, ModuleEnum::Roles);
                if ($request->has('profile_ids') && count($request->get('profile_ids')) > 0) {
                    $this->rBACLService_->attachProfilesToRole($role->id, $request->get('profile_ids'));
                }
                return $this->created(['message' => RBACLRolesController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $data = $this->model->getOnlyFillables($request->all());
            if($request->has('profile_ids') && $request->get('profile_ids') != null) {
                $this->rBACLService_->attachProfilesToRole($request->id, $request->profile_ids);
            }
            $result = $this->model->where('id', $request->id)->first();
            $oldData = $result->getOriginal();
            if ($result) {
                $result->update($data);
                $this->addLog(LogTypeEnum::Info, null, $oldData, $result, LogAction::Updated, ModuleEnum::Roles);
                return $this->created(['message' => RBACLRolesController::RECORD_UPDATED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show(Role $role)
    {
        try {
            $profiles = $role->profiles;
            return $this->created(['role' => $role]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try {
            $Role = Role::find($id);
            if ($Role) {
                $Role->delete();
                $this->addLog(LogTypeEnum::Info, null, $Role, null, LogAction::Deleted, ModuleEnum::Roles);
                return $this->created(['message' => RBACLRolesController::RECORD_DELETED]);
            }
            return $this->noRecord(['message' => RBACLRolesController::RECORD_NOT_FOUND]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function test()
    {
        dd($this->rBACLService_->removeProfileFromRole(1, 1));
    }
}
