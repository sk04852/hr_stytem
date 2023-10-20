<?php

namespace App\Http\Controllers\UserGroup;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserGroup\Models\UserGroup as ThisModel;
use App\Http\Controllers\UserGroup\Models\UserGroup;
use App\Http\Controllers\UserGroup\Requests\CreateGroupUserRequest;
use App\Http\Controllers\UserGroup\Requests\UpdateGroupUserRequest;
use App\Http\Controllers\UserGroup\Requests\DeleteGroupUserRequest;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
    const MODULE_NAME = 'GroupUser';
    const COLLECTION_NAME = 'GroupUser';
    const FAILED = 'Error In Updating Record';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $record = ThisModel::when(!empty($request->user_id), function ($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
            ->when(!empty($request->group_id), function ($query) use ($request) {
                return $query->where('group_id', $request->group_id);
            })
            ->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            if ($record->isNotEmpty()) {
                return $this->created([UserGroupController::COLLECTION_NAME => $record]);
            }
            return $this->noRecord(['message' => UserGroupController::RECORD_NOT_FOUND]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function check($request)
    {
        return UserGroup::where('user_id',$request->user_id)->where('group_id',$request->group_id)->exists();
    }

    public function store(CreateGroupUserRequest $request)
    {
        try {
            if($this->check($request)){
                return $this->created(['message' => 'User already has this group']);
            }
            $groupUser =  UserGroup::create($request->all());
            if ($groupUser) {
                return $this->created(['message' => UserGroupController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateGroupUserRequest $request)
    {
        try {
            $data = UserGroup::where('id', $request->id)->update($request->all());
            if ($data) {
                return $this->created(['message' => UserGroupController::RECORD_UPDATED]);
            } else {
                return $this->failed(['message' => UserGroupController::FAILED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteGroupUserRequest $request)
    {
        try {
            $record = $this->findOneById($request->id);
            if ($record->delete()) {
                return $this->created(['message' => UserGroupController::RECORD_DELETED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
