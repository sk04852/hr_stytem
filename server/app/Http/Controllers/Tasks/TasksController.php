<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Tasks\Requests\CreateTaskRequest;
use App\Http\Controllers\Tasks\Requests\DeleteTaskRequest;
use App\Http\Controllers\Tasks\Requests\UpdateTaskRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Tasks\Models\Task;
use Illuminate\Http\Request;
use Exception;

class TasksController extends Controller
{
    const MODULE_NAME = 'Tasks';
    const TASKS_CREATED = 'New Tasks created successfully';
    const TASKS_NOT_CREATED = 'Error in creating a Tasks';
    const COLLECTION_NAME = 'Tasks';
    const TASKS_UPDATED = 'Tasks updated successfully';
    const TASKS_NOT_UPDATED = 'Error in updating Tasks';
    const TASKS_DELETED = 'Tasks deleted successfully';
    const TASKS_NOT_DELETED = 'Error in deleting Tasks';
    const TASKS_ALREADY_MARKED = 'Tasks already marked';
    const TASKS_NOT_FOUND = 'Tasks not found';

    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $Tasks = $this->model->when(!empty($request->taskID), function ($query) use ($request) {
                return $query->where('Task-Id', $request->taskID);
            });
            if ($Tasks) {
                $Tasks = $this->model
                    ->orderBy($this->getSortBy(), $this->getSort())
                    ->paginate($this->getPerPage());
                if ($Tasks->isNotEmpty()) {
                    return $this->created([TasksController::COLLECTION_NAME => $Tasks]);
                }
            }
            return $this->noRecord(['message' => TasksController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateTaskRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => TasksController::TASKS_CREATED]);
            }
            return $this->failed(['message' => TasksController::TASKS_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateTaskRequest $request)
    {
        try {
            $updatedTasks = $this->model::where('id', $request->id)->update($request->all());
            if ($updatedTasks) {
                return $this->created(['message' => TasksController::TASKS_UPDATED]);
            }
            return $this->failed(['message' => TasksController::TASKS_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteTaskRequest $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => TasksController::TASKS_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

