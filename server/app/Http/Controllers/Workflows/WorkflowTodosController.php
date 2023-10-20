<?php
namespace App\Http\Controllers\Workflows;

use Exception;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Workflows\Models\WorkflowTodo as ThisModel;
use App\Http\Controllers\Workflows\Models\WorkflowEmail;
use App\Http\Controllers\Comments\Models\Comment;
use App\Http\Controllers\CalendarEvents\Models\CalendarEvent;
use App\Http\Controllers\Workflows\Models\Workflow;
use App\Http\Controllers\Workflows\Models\WorkflowInvokeFunction;
use App\Http\Services\CommentService;
use App\Http\Controllers\Workflows\Requests\CreateTodoWorkflowRequest as CreateRequest;
use App\Http\Controllers\Workflows\Requests\UpdateTodoWorkflowRequest as UpdateRequest;
use App\Http\Controllers\Workflows\Requests\DeleteTodoWorkflowRequest as DeleteRequest;
use App\Http\Services\WorkflowTodoDataService;
use Illuminate\Http\Request;


class WorkflowTodosController extends Controller
{
    private $workflowTodoDataService_;
    private $commentService_;

    const MODULE_NAME = 'workflow_todo';
    const COLLECTION_NAME = 'workflow_todos';
    const IS_TAMPLATE = true;
    const EVENT = 'Event';
    const TODO = 'Todo';
    const VISIBILITY = 'Public';
    const WORKFLOW_CALENDAR_ID = 2;
    const WORKFLOW_ID_REQUIRE = 'Workflow id is required';

    public function __construct(ThisModel $model, 
                                WorkflowTodoDataService $workflowTodoDataService, 
                                CommentService $commentService)
    {
        parent::__construct($model);
        $this->workflowTodoDataService_ = $workflowTodoDataService;
        $this->commentService_ = $commentService;
    }

    public function index(Request $request)
    {
        try {
            // dd(empty($request->workflow_id)); 
            if(empty($request->workflow_id))
                return $this->created(['message' => WorkflowTodosController::WORKFLOW_ID_REQUIRE]);
            $workflow = Workflow::where('id', $request->workflow_id)->forCompany($this->companyId())->first();
            if($workflow){
                $records = $this->model->where('workflow_id',$request->workflow_id)
                                    ->orderBy($this->getSortBy(), $this->getSort())
                                    ->paginate($this->getPerPage());
            }
            if (empty($records)) {
                return $this->failed(['message' => WorkflowTodosController::RECORD_NOT_FOUND]);
            }
            return $this->created([WorkflowTodosController::MODULE_NAME => $records]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $this->model->workflow_id = $request->workflow_id;
            $this->model->task_title = $request->task_title;
            $this->model->todo = $request->todo;
            switch ($request->todo) {
                case "mail":
                    $mail = $this->workflowTodoDataService_->emailTodo((int) getModuleIdFromEntity($this->model),
                                                                        (int) $this->userId(),
                                                                        (string) $request->subject,
                                                                        (string) ($request->has('from') && !empty($request->from)) ? $request->from : $this->user()->email,
                                                                        (string) ($request->has('from_name') && !empty($request->from_name)) ? $request->from_name : $this->user()->first_name,
                                                                        (string) $request->body,
                                                                        (string) $request->to,
                                                                        (string) ($request->has('cc') && !empty($request->cc)) ? $request->cc : '',
                                                                        (string) ($request->has('bcc') && !empty($request->from_name)) ? $request->from_name : '',
                                                                        (string) ($request->has('body_var') && !empty($request->body_var)) ? $request->body_var : '');
                    $this->model->data = $mail->id;
                break;
                case "event":
                    $event = $this->workflowTodoDataService_->todoOrEvent((int) WorkflowTodosController::WORKFLOW_CALENDAR_ID,
                                                                         (string) $request->subject,
                                                                         (string) WorkflowTodosController::EVENT,
                                                                         (string) $request->activity_type,
                                                                         $request->start_date_time,
                                                                         ($request->has('end_date_time') && !empty($request->end_date_time)) ? $request->end_date_time : null,
                                                                         (string) WorkflowTodosController::VISIBILITY,
                                                                         (bool) $request->notification,
                                                                         (string) $request->status,
                                                                         (string) $request->priority,
                                                                         (int) ($request->has('assigned_to') && !empty($request->assigned_to)) ? $request->assigned_to : 0,
                                                                         $this->userId(),
                                                                         (string) ($request->has('location') && !empty($request->location)) ? $request->location : null,
                                                                         (string) ($request->has('description') && !empty($request->description)) ? $request->description : null,
                                                                         (string) WorkflowTodosController::IS_TAMPLATE
                                                                        );
                    $this->model->data = $event->id;
                break;
                case "todo":
                    $todo = $this->workflowTodoDataService_->todoOrEvent((int) WorkflowTodosController::WORKFLOW_CALENDAR_ID,
                                                                         (string) $request->subject,
                                                                         (string) WorkflowTodosController::TODO,
                                                                         (string) ($request->has('activity_type') && !empty($request->activity_type)) ? $request->activity_type : 'Other',
                                                                         $request->start_date_time,
                                                                         ($request->has('end_date_time') && !empty($request->end_date_time)) ? $request->end_date_time : null,
                                                                         (string) WorkflowTodosController::VISIBILITY,
                                                                         (bool) $request->notification,
                                                                         (string) $request->status,
                                                                         (string) $request->priority,
                                                                         (string) ($request->has('assigned_to') && !empty($request->assigned_to)) ? $request->assigned_to : 0,
                                                                         $this->userId(),
                                                                         (string) ($request->has('location') && !empty($request->location)) ? $request->location : null,
                                                                         (string) ($request->has('description') && !empty($request->description)) ? $request->description : null,
                                                                         (string) WorkflowTodosController::IS_TAMPLATE
                                                                        );
                    $this->model->data = $todo->id;
                break;
                case "function":
                    $function = $this->workflowTodoDataService_->functionTodo((string) $request->class_name,
                                                                              (string) $request->function_name,
                                                                              (string) ($request->has('function_parameters') && !empty($request->function_parameters)) ? $request->function_parameters : ''
                                                                            );
                    $this->model->data = $function->id;
                break;
                case "entity":
                    $this->model->module_id = $request->module_id;
                    $uuid = Str::uuid();
                    $this->model->data = $this->commentService_->createComment($request->comment, 
                                                                $this->userId(),
                                                                WorkflowTodosController::IS_TAMPLATE, 
                                                                getModuleIdFromEntity($this->model),
                                                                $this->model->workflow_id,
                                                                $uuid
                                                                );
                break;
                default:
                break;
            }
            if($this->model->save()) {
                return $this->created([WorkflowTodosController::MODULE_NAME => $this->model, 'message'=> WorkflowTodosController::RECORD_CREATED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(Request $request, UpdateRequest $validater)
    {
        $validater->idValidate($request);
        try{
            $record = $this->model->find($request->id);
            $workflow = Workflow::where('id', $record->workflow_id)->forCompany($this->companyId())->first();
            if(!$workflow)
                return $this->notAllowed(["message" => WorkflowTodosController::UNAUTHORIZED]);
            $record->task_title = $request->task_title;
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
        $validater->todoValidate($request, $record);
        try {
            $updateData = $request->except(["id","task_title"]);
            switch ($record->todo) {
                case "mail":
                    $savedData = WorkflowEmail::where('id', $record->data)->update($updateData);
                break;
                case "event":
                    $savedData = CalendarEvent::where('id', $record->data)->update($updateData);
                break;
                case "todo":
                    $savedData = CalendarEvent::where('id', $record->data)->update($updateData);
                break;
                case "function":
                    $savedData = WorkflowInvokeFunction::where('id', $record->data)->update($updateData);
                break;
                case "entity":
                    $savedData = Comment::where('id', $record->data)->update($updateData);
                break;
                default:
                    return null;
            }
            if($savedData) {
                return $this->created([WorkflowTodosController::MODULE_NAME => $record, 'message'=> WorkflowTodosController::RECORD_UPDATED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteRequest $request)
    {
        try {
            try {
                $record =$this->model->find($request->id);
                $workflow = Workflow::where('id', $record->workflow_id)->forCompany($this->companyId())->first();
                if(!$workflow)
                    return $this->notAllowed(["message" => WorkflowTodosController::UNAUTHORIZED]);
                $todo = $record->todo;
                $todoDataId = $record->data;
                if ($record->delete()) {
                    $this->workflowTodoDataService_->destroyTodoData($todo, $todoDataId);
                    return $this->created(['message' => WorkflowTodosController::RECORD_DELETED]);
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                return $this->serverSQLError($ex);
            } catch (Exception $ex) {
                return $this->serverError($ex);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function showTodoData($workflowTodo, $todoDataId) {
        try {
            $record = $this->workflowTodoDataService_->showTodoData($workflowTodo, $todoDataId);
            if(empty($record)){
                return $this->created(['message' => WorkflowTodosController::RECORD_NOT_FOUND]);
            }
            return $this->created([WorkflowTodosController::MODULE_NAME => $record]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
