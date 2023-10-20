<?php
/**
 * HrTasksController
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\HrTasks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HrTasks\Requests\CreateHRTaskRequest;
use App\Http\Controllers\HrTasks\Requests\DeleteHRTaskRequest;
use App\Http\Controllers\HrTasks\Requests\GetMentionRequest;
use App\Http\Controllers\HrTasks\Requests\MarkHRTaskCompleteRequest;
use App\Http\Services\HrTaskService;
use App\Models\UserPr;
use App\Notifications\HrTaskCreated;
use App\Notifications\HrTaskCompleted;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Controllers\HrTasks\Models\HrTask;
use Illuminate\Support\Facades\Notification;

class HrTasksController extends Controller
{

    const IN_PROGRESS_STATUS = 1;
    const COMPLETED_STATUS = 2;
    const MODULE_NAME = 'HrTasks';
    const COLLECTION_NAME = 'HrTasks';
    const RECORD_NOT_FOUND = 'Tasks not found';
    const TASK_CREATED = 'New task created successfully';
    const TASK_NOT_CREATED = 'Error in creating a task';
    const TASK_UPDATED = 'task updated successfully';
    const TASK_NOT_UPDATED = 'Error in updating task';
    const TASK_DELETED = 'Task deleted successfully';
    const TASK_NOT_DELETED = 'Error in deleting task';
    const IN_COMPLETE_TASK_MESSAGE = 'In-Complete Task can not be delete';
    const MARK_COMPLETE_MESSAGE = 'Task Status is marked completed';
    const ATTEMPT_TO_MARK_COMPLETED = "Task is all Ready completed by another user";

    public function __construct(HrTask $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $tasks = QueryBuilder::for($this->model)
                ->with(['status'])
                ->allowedFilters(['description', 'deadline', 'status.id'])
                ->defaultSort('-id')
                ->allowedSorts(['description', 'deadline'])
                ->paginate($this->getPerPage())
                ->appends(request()->query());

            if ($tasks->isNotEmpty()) {
                return $tasks;
            }

            return $this->noRecord(['message' => HrTasksController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function store(CreateHRTaskRequest $request)
    {
        try {
            DB::transaction(function () use ($request){
                $creator = Auth::user();

                $task_data = $request->except('user_pr_ids');
                $task_data['hr_task_status_id'] = HrTasksController::IN_PROGRESS_STATUS;
                $task_data['created_by'] = Auth::id();

                $new_task =  HrTask::create($task_data);
                $new_task->assignedUsers()->attach($request->get('user_pr_ids'));

                $users = UserPr::whereIn('id', $request->get('user_pr_ids'))->get();
                if($users->isNotEmpty()){
                    foreach ($users as $user){
                        Notification::send($user, new HrTaskCreated($new_task, $creator, $user));
                    }
                }
            });


            return $this->created(['message' => HrTasksController::TASK_CREATED], 200);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function delete(DeleteHRTaskRequest $request)
    {
        try {
            $task = HrTask::where('id', $request->id)->first();
            if(is_null($task)){
                return $this->noRecord(['message' => HrTasksController::RECORD_NOT_FOUND]);
            }

//             if(!$this->checkStatus($task,HrTasksController::COMPLETED_STATUS)){
//                 return $this->noRecord(['message' => HrTasksController::IN_COMPLETE_TASK_MESSAGE]);
//             }

            if($task->delete()){
                return $this->created(['message' => HrTasksController::TASK_DELETED], 200);
            }else{
                return $this->failed(['message' => HrTasksController::TASK_NOT_DELETED], 200);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }


    /**
     * Check the Task status.
     *
     * @param $task_object
     * @param $status
     * @return boolean
     */
    protected function checkStatus($task_object, $status){
        return $task_object->hr_task_status_id == $status;
    }

    public function markComplete(MarkHRTaskCompleteRequest $request)
    {
        try {
            $achiever = Auth::user();

            $task = HrTask::where('id', $request->id)->first();
            if(is_null($task)){
                return $this->noRecord(['message' => HrTasksController::RECORD_NOT_FOUND]);
            }

            if($this->checkStatus($task,HrTasksController::COMPLETED_STATUS)){
                return $this->failed(['message' => HrTasksController::ATTEMPT_TO_MARK_COMPLETED]);
            }

            if($task->update(['hr_task_status_id' => HrTasksController::COMPLETED_STATUS])){
                $task_creator = UserPr::where(['id' => $task->created_by])->first();

                Notification::send($task_creator, new HrTaskCompleted([
                    'title' => $achiever->user->name . ' on ülesande täitnud',
                    'task_id' => $task->id,
                    'description' => $task->description,
                    'deadline' => $task->deadline,
                    'image' => $achiever->photo
                ]));

                return $this->created(['message' => HrTasksController::MARK_COMPLETE_MESSAGE], 200);
            }else{
                return $this->failed(['message' => HrTasksController::TASK_NOT_UPDATED], 200);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function getCreatedTasks(Request $request)
    {
        try {
            $user = $request->user();

            $query = $this->model->where('created_by', $user->id);

            $tasks = QueryBuilder::for($query)
                ->with(['status'])
                ->allowedFilters(['description', 'deadline', 'status.id'])
                ->defaultSort('-id')
                ->allowedSorts(['description', 'deadline'])
                ->paginate($this->getPerPage())
                ->appends(request()->query());

            if ($tasks->isNotEmpty()) {
                return $tasks;
            }

            return $this->noRecord(['message' => HrTasksController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function getAssignedTasks(Request $request)
    {
        try {
            $user = $request->user();

            $query = $this->model
                ->join('hr_assigned_tasks', 'hr_tasks.id', '=', 'hr_assigned_tasks.hr_task_id')
                ->where('user_pr_id', $user->id);

            $tasks = QueryBuilder::for($query)
                ->with(['status'])
                ->allowedFilters(['description', 'deadline', 'status.id'])
                ->defaultSort('-id')
                ->allowedSorts(['description', 'deadline'])
                ->paginate($this->getPerPage())
                ->appends(request()->query());

            if ($tasks->isNotEmpty()) {
                return $tasks;
            }

            return $this->noRecord(['message' => HrTasksController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function getMention(GetMentionRequest $request, HrTaskService $hrTaskService) {
        try {
            $serach = $request->search;

            $result = $hrTaskService->getMention($serach);

            return $this->created(['result' => $result], 200);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }


}
