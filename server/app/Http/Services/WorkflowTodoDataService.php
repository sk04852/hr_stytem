<?php
namespace App\Http\Services;

use App\Http\Controllers\CalendarEvents\Models\CalendarEvent;
use App\Http\Controllers\Comments\Models\Comment;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Workflows\Models\WorkflowEmail;
use App\Http\Controllers\Workflows\Models\WorkflowInvokeFunction;

class WorkflowTodoDataService{
    private $workflowEmail_;
    private $workflowInvokeFunction_;
    private $calendarEvent_;

    public function __construct(WorkflowEmail $workflowEmail,
                                WorkflowInvokeFunction $workflowInvokeFunction,
                                CalendarEvent $calendarEvent) {
        $this->workflowEmail_ = $workflowEmail;
        $this->workflowInvokeFunction_ = $workflowInvokeFunction;
        $this->calendarEvent_ = $calendarEvent;

    }
    public function destroyTodoData($todo, $todoDataId)
    {
        switch ($todo) {
            case "mail":
                $this->workflowEmail_->destroy($todoDataId);
            break;
            case "event":
                $this->calendarEvent_->destroy($todoDataId);
            break;
            case "todo":
                $this->calendarEvent_->destroy($todoDataId);
            break;
            case "function":
                $this->workflowInvokeFunction_->destroy($todoDataId);
            break;
            case "entity":
                Comment::destroy($todoDataId);
            break;
            default:
                return null;
        }
    }
    
    public function showTodoData($workflowTodo, $todoDataId) {
        $record = null;
        switch ($workflowTodo) {
            case "mail":
                $record = $this->workflowEmail_->find($todoDataId);
            break;
            case "event":
                $record = $this->calendarEvent_->find($todoDataId);
            break;
            case "todo":
                $record = $this->calendarEvent_->find($todoDataId);
            break;
            case "function":
                $record = $this->workflowInvokeFunction_->find($todoDataId);
            break;
            case "entity":
                $record = Comment::find($todoDataId);
            break;
            default:
                $record = null;
        }
        return $record;
    }
    public function emailTodo(int $moduleId, int $relationId, string $subject, string $from, string $fromName, string $body, string $to, string $cc, string $bcc, string $bodyVar)
    {
        $this->workflowEmail_->relation_id = $relationId;
        $this->workflowEmail_->module_id = $moduleId;
        $this->workflowEmail_->subject = $subject;
        $this->workflowEmail_->from = $from;
        $this->workflowEmail_->from_name = $fromName;
        $this->workflowEmail_->body = $body;
        $this->workflowEmail_->to = $to;
        $this->workflowEmail_->cc = $cc;
        $this->workflowEmail_->bcc = $bcc;
        $this->workflowEmail_->body_var = $bodyVar;
        $this->workflowEmail_->save();
        return $this->workflowEmail_;
    }

    public function todoOrEvent(int $calenderId, string $subject, string $type, string $activityType , $startDateTime, $endDateTime, string $visibility, bool $notification, string $status, string $priority, $assignedTo, int $createdBy, $location, $description, bool $isTemplate)
    {
        $this->calendarEvent_->calendar_id = $calenderId;
        $this->calendarEvent_->subject = $subject;
        $this->calendarEvent_->type = $type;
        $this->calendarEvent_->activity_type = $activityType;
        $this->calendarEvent_->start_date_time = $startDateTime;
        $this->calendarEvent_->end_date_time = $endDateTime;
        $this->calendarEvent_->visibility = $visibility;
        $this->calendarEvent_->notification = $notification;
        $this->calendarEvent_->status = $status;
        $this->calendarEvent_->priority = $priority;
        if($assignedTo != 0)
            $this->calendarEvent_->assigned_to = $assignedTo;
        $this->calendarEvent_->created_by = $createdBy;
        $this->calendarEvent_->location = $location;
        $this->calendarEvent_->description = $description;
        $this->calendarEvent_->is_template = $isTemplate;
        $this->calendarEvent_->save();
        return $this->calendarEvent_;
    }

    public function functionTodo(string $className, string $functionName, string $functionParameters)
    {
        $this->workflowInvokeFunction_->class_name = $className;
		$this->workflowInvokeFunction_->function_name = $functionName;
		$this->workflowInvokeFunction_->function_parameters = $functionParameters;
        $this->workflowInvokeFunction_->save();
        return $this->workflowInvokeFunction_;
    }   
}

?>