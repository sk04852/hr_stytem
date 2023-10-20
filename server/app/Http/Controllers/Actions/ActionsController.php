<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Actions\Models\Action;
use App\Http\Controllers\Actions\Requests\CreateActionRequest;
use App\Http\Controllers\Actions\Requests\DeleteActionRequest;
use App\Http\Controllers\Actions\Requests\UpdateActionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class ActionsController extends Controller
{
    const MODULE_NAME = 'action';
    const ACTION_CREATED = 'New Action created successfully';
    const ACTION_NOT_CREATED = 'Error in creating a Action';
    const COLLECTION_NAME = 'Actions';
    const ACTION_UPDATED = 'Action updated successfully';
    const ACTION_NOT_UPDATED = 'Error in updating Action';
    const ACTION_DELETED = 'Action deleted successfully';
    const ACTION_NOT_DELETED = 'Error in deleting Action';
    const ACTION_ALREADY_MARKED = 'Action already marked';
    const ACTION_NOT_FOUND = 'Action not found';

    public function __construct(Action $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $action_name = $this->model->when(!empty($request->action_name), function ($query) use ($request) {
                return $query->where('Action-ID', $request->action_name);
            });
              if ($action_name) {
                  $action_name = $this->model
                ->when(!empty($request->action_name), function ($query) use ($request) {
                    return $query->where('Action-ID', $request->action_name);
                })
            ->orderBy($this->getSortBy(), $this->getSort())
            ->paginate($this->getPerPage());
                  if ($action_name->isNotEmpty()) {
                      return $this->created([ActionsController::COLLECTION_NAME => $action_name]);
                  }
              }
            return $this->noRecord(['message' => ActionsController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function store(CreateActionRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => ActionsController::ACTION_CREATED]);
            }
            return $this->failed(['message' => ActionsController::ACTION_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateActionRequest $request)
    {
        try {
            $updatedAction = $this->model::where('id', $request->id)->update($request->all());
            if ($updatedAction) {
                return $this->created(['message' => ActionsController::ACTION_UPDATED]);
            }
            return $this->failed(['message' => ActionsController::ACTION_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteActionRequest $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => ActionsController::ACTION_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    }

