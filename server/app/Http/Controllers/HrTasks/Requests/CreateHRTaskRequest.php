<?php

namespace App\Http\Controllers\HrTasks\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateHRTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_pr_ids' => 'required|array',
            'user_pr_ids.*' => 'exists:user_pr,id',
            // 'description' => 'required|max:191|unique:mail_templates,template_key',
            'description' => 'required|max:191',
            'deadline' => 'required|date_format:Y-m-d',
        ];
    }
}
