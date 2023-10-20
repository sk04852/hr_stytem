<?php

namespace App\Http\Controllers\UserTags;

use App\Http\Controllers\UserTags\Models\UserTag;
use App\Http\Controllers\UserTags\Requests\CreateUserTagRequest;
use App\Http\Controllers\UserTags\Requests\DeleteUserTagRequest;
use App\Http\Controllers\UserTags\Requests\UpdateUserTagRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class UserTagsController extends Controller
{
    const MODULE_NAME = 'UserTag';
    const USER_TAG_CREATED = 'New UserTag created successfully';
    const USER_TAG_NOT_CREATED = 'Error in creating a UserTag';
    const COLLECTION_NAME = 'UserTags';
    const USER_TAG_UPDATED = 'UserTag updated successfully';
    const USER_TAG_NOT_UPDATED = 'Error in updating UserTag';
    const USER_TAG_DELETED = 'UserTag deleted successfully';
    const USER_TAG_NOT_DELETED = 'Error in deleting UserTag';
    const USER_TAG_ALREADY_MARKED = 'UserTag already marked';
    const USER_TAG_NOT_FOUND = 'UserTag not found';

    public function __construct(UserTag $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $UserTag = $this->model->when(!empty($request->TagID), function ($query) use ($request) {
                return $query->where('Tag-ID', $request->TagID);
            });
            if ($UserTag) {
                $UserTag = $this->model
                    ->when(!empty($request->TagID), function ($query) use ($request) {
                        return $query->where('Tag-ID', $request->TagID);
                    })
                    ->orderBy($this->getSortBy(), $this->getSort())
                    ->paginate($this->getPerPage());
                if ($UserTag->isNotEmpty()) {
                    return $this->created([UserTagsController::COLLECTION_NAME => $UserTag]);
                }
            }
            return $this->noRecord(['message' => UserTagsController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateUserTagRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => UserTagsController::USER_TAG_CREATED]);
            }
            return $this->failed(['message' => UserTagsController::USER_TAG_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateUserTagRequest $request)
    {
        try {
            $updatedUserTag = $this->model::where('id', $request->id)->update($request->all());
            if ($updatedUserTag) {
                return $this->created(['message' => UserTagsController::USER_TAG_UPDATED]);
            }
            return $this->failed(['message' => UserTagsController::USER_TAG_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteUserTagRequest $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => UserTagsController::USER_TAG_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

