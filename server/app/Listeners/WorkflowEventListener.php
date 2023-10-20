<?php

namespace App\Listeners;

use Exception;
use Carbon\Carbon;
use App\Mail\WorkflowEmail;
use App\Events\WorkflowEvent;
use App\Http\Services\CommentService;
use App\Http\Services\GetWorkflowVariableService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Comments\Models\Comment;
use App\Http\Controllers\Workflows\Models\Workflow;
use App\Http\Controllers\Workflows\Models\WorkflowEmail as ModelEmail;
use App\Http\Controllers\CalendarEvents\Models\CalendarEvent;
use App\Http\Controllers\Workflows\Models\WorkflowInvokeFunction;
use App\Http\Controllers\Workflows\Models\WorkflowTodo;
use Illuminate\Support\Facades\Mail;

class WorkflowEventListener extends Controller
{
    const IS_TEMPLATE = false;
    const FIRST_SAVE = 'first save';
    const EVERYTIME_SAVE = 'everytime saved';
    const EVERYTIME_MODIFIED = 'everytime modified';
    private $commentService_;
    private $getWorkflowVariableService_;
    public function __construct(CommentService $commentService, GetWorkflowVariableService $getWorkflowVariableService)
    {
        $this->commentService_ = $commentService;
        $this->getWorkflowVariableService_ = $getWorkflowVariableService;
    }

    public function handle(WorkflowEvent $event)
    {
        try {
            if($event->crudOption == "create") {
                $workflowIds = $this->workflowIds($event->moduleId, WorkflowEventListener::FIRST_SAVE);
                $todos = $this->workflowTodos($workflowIds);
                $this->actionLoop($todos, $event->model);
            }
        
            if($event->crudOption == "saved") {
                $workflowIds = $this->workflowIds($event->moduleId, WorkflowEventListener::EVERYTIME_SAVE);
                $todos = $this->workflowTodos($workflowIds);
                $this->actionLoop($todos, $event->model);
            }

            if($event->crudOption == "delete" || $event->crudOption == "update") {
                $workflowIds = $this->workflowIds($event->moduleId, WorkflowEventListener::EVERYTIME_MODIFIED);
                $todos = $this->workflowTodos($workflowIds);
                $this->actionLoop($todos, $event->model);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    
    public function workflowIds($moduleId, $conditon){
        return Workflow::select('id')->where('module_id', $moduleId)
                                    ->where('execution', $conditon)
                                    ->get();
    }
    
    public function workflowTodos($workflowIds){
        return WorkflowTodo::whereIn('workflow_id', $workflowIds)->get();
    }
    
    public function actionLoop($todos, $model){
        foreach($todos as $workflow) {
            
            if($workflow->todo == "mail") {
                $emailData = ModelEmail::where('id',$workflow->data)->first();
                if($emailData) {
                    $emails = explode(',', $emailData->to);
                    foreach($emails as $key => $mailTo) {
                        $firstCharacter = substr($mailTo, 0, 1);
                        if($firstCharacter == '$') {
                            $varName = substr($mailTo, 1);
                            $mailTo = $this->getWorkflowVariableService_->find($model, $varName);
                        }
                        if(!empty($emailData->body_var)) {
                            if($key == 0) {
                                $bodyVariables = $this->getWorkflowVariableService_->getVar($model, $emailData->body_var);
                                $emailData = $emailData->toArray();
                                $emailData = $emailData + $bodyVariables;
                            }
                        }
                        else
                        {
                            if($key == 0)
                                $emailData = $emailData->toArray();
                        }
                        Mail::to($mailTo)->send(new WorkflowEmail($emailData));
                    }
                }
            }
            elseif($workflow->todo == "function"){
                $functionData = WorkflowInvokeFunction::where('id',$workflow->data)->first();
                if($functionData) {
                    $class = $functionData->class_name;
                    $function = $functionData->function_name;
                    $param = $functionData->function_parameters;
                    $exampleController = app()->make($class);
                    $param == "" ? $exampleController->$function() : $exampleController->$function($param);
                }
            }
            elseif($workflow->todo == "todo"){
                $todoTemplate = CalendarEvent::where('id', $workflow->data)->first();
                if($todoTemplate) {
                    $this->saveTodoOrEvent($todoTemplate);
                }
            }
            elseif($workflow->todo == "event"){
                $todoTemplate = CalendarEvent::where('id', $workflow->data)->first();
                if($todoTemplate)
                $this->saveTodoOrEvent($todoTemplate);
            }
            elseif($workflow->todo == "Update fields"){
                
            }
            elseif($workflow->todo == "entity"){
                $commentTemplate = Comment::find($workflow->data);
                if($commentTemplate){
                    $this->commentService_->createComment($commentTemplate->comment,
                                            $commentTemplate->created_by, 
                                            WorkflowEventListener::IS_TEMPLATE,
                                            $commentTemplate->module, 
                                            $commentTemplate->relation_id, 
                                            $commentTemplate->uuid
                                            );
                }
                
            }
        }
    }

    public function saveTodoOrEvent($data) {
        $new = new CalendarEvent;
        $new->calendar_id = $data->calendar_id;
        $new->subject = $data->subject;
        $start_date = Carbon::now()->toDateString();
        $start_time = $data->start_date_time;
        $start_time = Carbon::create($start_time)->toTimeString();
        $new->start_date_time = $start_date . ' ' . $start_time;
        $new->end_date_time = $data->end_date_time;
        $new->activity_type = $data->activity_type;
        $new->visibility = $data->visibility;
        $new->status = $data->status;
        $new->assigned_to = $data->assigned_to;
        $new->created_by = $data->created_by;
        $new->description = $data->description;
        $new->priority = $data->priority;
        $new->type = $data->type;
        $new->is_template = WorkflowEventListener::IS_TEMPLATE;
        $new->save();
    }
}
