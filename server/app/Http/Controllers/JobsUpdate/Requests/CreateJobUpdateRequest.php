<?php

namespace App\Http\Controllers\JobsUpdate\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateJobUpdateRequest extends FormRequest
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
            'job_ID' => 'nullable',
            'title' => 'nullable',
            'status' => 'nullable',
            'type_of_job' => 'nullable',
            'category_ID' => 'nullable',
            'update_ID'=>'nullable',
            'category' => 'nullable',
            'location' => 'nullable',
            'contact' => 'nullable',
            'doc_ID' => 'nullable',
            'doc' => 'nullable',
            'jobDescription' => 'nullable',
            'divDescription' => 'nullable',
            'comments' => 'nullable',
            'benefits' => 'nullable',
            'tag_ID' => 'nullable|exists:job-tag,id',
        ];
    }
}
