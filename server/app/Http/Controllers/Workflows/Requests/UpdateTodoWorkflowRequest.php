<?php

namespace App\Http\Controllers\Workflows\Requests;

use Illuminate\Validation\Rule;

class UpdateTodoWorkflowRequest
{
    public function todoValidate($request,$todo)
    {
        $request->validate([  
            "from" => 'nullable',
            "to" => Rule::requiredIf($todo->todo == 'mail'),
            "body" => Rule::requiredIf($todo->todo == 'mail'),
            // todo or event
            "subject" => [Rule::requiredIf($todo->todo == 'todo'),
                            Rule::requiredIf($todo->todo == 'event'), 
                            Rule::requiredIf($todo->todo == 'mail')],
            "description" => ['nullable'],
            "status" => [Rule::requiredIf($todo->todo == 'todo'), 
                        Rule::requiredIf($todo->todo == 'event')],
            "priority" => [Rule::requiredIf($todo->todo == 'todo'), 
                            Rule::requiredIf($todo->todo == 'event')],
            "assigned_to" => ['nullable'],
            "start_date_time" => [Rule::requiredIf($todo->todo == 'todo'), 
                                    Rule::requiredIf($todo->todo == 'event')],
            "end_date_time" => ['nullable', 'after:start_date_time'],
            "notification" => [Rule::requiredIf($todo->todo == 'todo'), 
                                Rule::requiredIf($todo->todo == 'event')],
            "activity_type" => [Rule::requiredIf($todo->todo == 'event')],
            //create entity
            //comment
            "comment" => [Rule::requiredIf($todo->module_id == 10)],
            //function
            "class_name" => [Rule::requiredIf($todo->todo == 'function')],
            "function_name" => [Rule::requiredIf($todo->todo == 'function')],
            "function_parameters" => ['nullable']
            ]);        
    }
    public function idValidate($request)
    {       
        $request->validate([
            "id" => ["required", "exists:workflow_todos,id"],
            "task_title" => "required"
        ]);
    }
}
