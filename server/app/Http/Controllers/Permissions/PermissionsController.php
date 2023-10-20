<?php

namespace App\Http\Controllers\Permissions;

use App\Http\Controllers\Permissions\Models\Permission;
use App\Http\Controllers\Permissions\Requests\CreatePermissionRequest;
use App\Http\Controllers\Permissions\Requests\DeletePermissionRequest;
use App\Http\Controllers\Permissions\Requests\UpdatePermissionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class PermissionsController extends Controller
{
    const MODULE_NAME = 'Permission';
    const PERMISSSION_CREATED = 'New Permission created successfully';
    const PERMISSSION_NOT_CREATED = 'Error in creating a Permission';
    const COLLECTION_NAME = 'Permissions';
    const PERMISSSION_UPDATED = 'Permission updated successfully';
    const PERMISSSION_NOT_UPDATED = 'Error in updating Permission';
    const PERMISSSION_DELETED = 'Permission deleted successfully';
    const PERMISSSION_NOT_DELETED = 'Error in deleting Permission';
    const PERMISSSION_ALREADY_MARKED = 'Permission already marked';
    const PERMISSSION_NOT_FOUND = 'Permission not found';

    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $Permission = $this->model->when(!empty($request->RoleID), function ($query) use ($request) {
                return $query->where('Role-ID', $request->RoleID);
            });
              if ($Permission) {
                  $Permission = $this->model
                ->when(!empty($request->RoleID), function ($query) use ($request) {
                    return $query->where('Role-ID', $request->RoleID);
                })
            ->orderBy($this->getSortBy(), $this->getSort())
            ->paginate($this->getPerPage());
                  if ($Permission->isNotEmpty()) {
                      return $this->created([PermissionsController::COLLECTION_NAME => $Permission]);
                  }
              }
            return $this->noRecord(['message' => PermissionsController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreatePermissionRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => PermissionsController::PERMISSSION_CREATED]);
            }
            return $this->failed(['message' => PermissionsController::PERMISSSION_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdatePermissionRequest $request)
    {
        try {
            $updatedPermission = $this->model::where('id', $request->id)->update($request->all());
            if ($updatedPermission) {
                return $this->created(['message' => PermissionsController::PERMISSSION_UPDATED]);
            }
            return $this->failed(['message' => PermissionsController::PERMISSSION_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeletePermissionRequest $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => PermissionsController::PERMISSSION_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    }

