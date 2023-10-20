<?php

namespace App\Http\Controllers\Acl\Permissions;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Acl\Permissions\Requests\CreatePermissionRequest as CreateRequest;
use App\Http\Controllers\Acl\Permissions\Requests\UpdatePermissionRequest as UpdateRequest;
use App\Http\Controllers\Acl\Models\Permission;
use App\Http\Controllers\Modules\Enums\LogAction;
use App\Http\Controllers\Modules\Enums\LogTypeEnum;
use App\Http\Controllers\Modules\Enums\ModuleEnum;
use Exception;
use Illuminate\Support\Facades\Log;

class PermissionsController extends Controller
{

    const RECORD_CREATED = 'Permission has been created successfuly';
    const RECORD_UPDATED = 'Permission has been updated successfuly';
    const RECORD_DELETED = 'Permission has been deleted successfuly';
    const RECORD_NOT_FOUND = 'Permission not found';
    const COLLECTION_NAME = 'permissions';

    public function __construct()
    {
    }

    public function index()
    {
        try {
            $data = Permission::orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            if ($data->isNotEmpty()) {
                return $this->created([PermissionsController::COLLECTION_NAME => $data]);
            }
            return $this->noRecord(['message' => PermissionsController::RECORD_NOT_FOUND]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $result = Permission::create($request->all());
            if ($result) {
                $this->addLog(LogTypeEnum::Info, null, $result, $result, LogAction::Created, ModuleEnum::Permissions);
                return $this->created(['message' => PermissionsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $result = Permission::where('id', $request->id)->first();
            $oldData = $result->getOriginal();
            if ($result) {
                $result->update($request->all());
                $this->addLog(LogTypeEnum::Info, null, $oldData, $result, LogAction::Updated, ModuleEnum::Permissions);
                return $this->created(['message' => PermissionsController::RECORD_UPDATED]);
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
            $permission = Permission::find($id);
            if ($permission) {
                $permission->delete();
                $this->addLog(LogTypeEnum::Info, null, $permission, null, LogAction::Deleted, ModuleEnum::Permissions);
                return $this->created(['message' => PermissionsController::RECORD_DELETED]);
            }
            return $this->noRecord(['message' => PermissionsController::RECORD_NOT_FOUND]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
