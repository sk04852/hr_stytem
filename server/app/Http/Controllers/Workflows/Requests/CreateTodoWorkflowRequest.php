<?php

namespace App\Http\Controllers\Workflows\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTodoWorkflowRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "workflow_id" => "required||exists:workflows,id",
            "task_title" => "required",
            "todo" => ['required', Rule::in(['mail', 'function', 'todo', 'event', 'Update fields', 'entity'])],
            //mail
            "from" => ['nullable'],
            "to" => [$this->mail()],
            "body" => [$this->mail()],
            //todo or event
            "subject" => [$this->todo(), $this->event(), $this->mail()],
            "description" => ['nullable'],
            "status" => [$this->todo(), $this->event()],
            "priority" => [$this->todo(), $this->event()],
            "assigned_to" => ['nullable'],
            "start_date_time" => [$this->todo(), $this->event()],
            "end_date_time" => ['nullable', 'after:start_date_time'],
            "notification" => [$this->todo(), $this->event()],
            "activity_type" => [$this->event()],
            //create entity
            "module_id" => [$this->entity()],
            //comment
            "comment" => [$this->comment()],
            //function
            "class_name" => [$this->functiontodo()],
            "function_name" => [$this->functiontodo()],
            "function_parameters" => ['nullable'],
        ];        
    }

    public function mail(){
        return Rule::requiredIf($this->todo == 'mail');
    }
   
    public function todo(){
        return Rule::requiredIf($this->todo == 'todo');
    }
    
    public function event(){
        return Rule::requiredIf($this->todo == 'event');
    }

    public function entity(){
        return Rule::requiredIf($this->todo == 'entity');
    }
    
    public function comment(){
        return Rule::requiredIf($this->module_id == 10);
    }

    public function functiontodo(){
        return Rule::requiredIf($this->todo == 'function');
    }
}
