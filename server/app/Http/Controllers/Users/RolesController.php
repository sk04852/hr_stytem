<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\Requests\AssignRolePermissionsRequest;
use App\Http\Controllers\Users\Requests\CreateRoleRequest;
use App\Http\Controllers\Users\Requests\DeleteRoleRequest;
use App\Http\Controllers\Users\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    const MODULE_NAME = 'Roles';
    const COLLECTION_NAME = 'Roles';
    const RECORD_NOT_FOUND = 'Roles not found';
    const ROLE_CREATED = 'New role created successfully';
    const ROLE_NOT_CREATED = 'Error in creating a role';
    const ROLE_UPDATED = 'Role updated successfully';
    const ROLE_NOT_UPDATED = 'Error in updating role';
    const ROLE_DELETED = 'Role deleted successfully';
    const ROLE_NOT_DELETED = 'Error in deleting role';
    const ASSIGNED_ROLE_PERMISSIONS = 'Role permissions assigned';

    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $roles = $this->model
                ->orderBy($this->getSortBy(), $this->getSort())
                ->paginate($this->getPerPage());
            if ($roles->isNotEmpty()) {
                return $this->created([RolesController::COLLECTION_NAME => $roles]);
            }

            return $this->noRecord(['message' => RolesController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function store(CreateRoleRequest $request)
    {
        try {
            $role = $request->all();
            $role['guard_name'] = 'api';
            $response = Role::create($role);
            if ($response) {
                return $this->created([
                    'message' => RolesController::ROLE_CREATED,
                    'data' => $response->toArray()
                ]);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function show($id, $permissions = null)
    {
        try {
            if(is_null($permissions)){
                $role = Role::where('id', $id)->first();
            }else {
                $role = Role::where('id', $id)->with('permissions')->first();
            }

            if (is_null($role)) {
                return $this->noRecord(['message' => RolesController::RECORD_NOT_FOUND]);
            } else {
                return $this->created(['role' => $role]);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRoleRequest $request)
    {
        try {
            $response = Role::where('id', $request->id)->update(['name' => $request->name]);
            if ($response) {
                return $this->created(['message' => RolesController::ROLE_UPDATED]);
            } else {
                return $this->failed(['message' => RolesController::ROLE_NOT_UPDATED]);

            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function delete(DeleteRoleRequest $request)
    {
        try {
            $role = Role::where('id', $request->id)->first();
            if (is_null($role)) {
                return $this->noRecord(['message' => RolesController::RECORD_NOT_FOUND]);
            } else {
                DB::transaction(function () use (&$role){
                    $role->permissions()->delete();
                    $role->delete();
                });
                return $this->created(['message' => RolesController::ROLE_DELETED]);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function permissions(Request $request)
    {
        try {
            $permissions = Permission::all();

            return $this->noRecord(['permissions' => $permissions], 200);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }


    public function assignPermissions (AssignRolePermissionsRequest $request){
        try {
            $role = Role::where('id', $request->role_id)->first();
            $role->syncPermissions($request->permissions);

            return $this->created(['permissions' => RolesController::ASSIGNED_ROLE_PERMISSIONS], 200);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }
}

