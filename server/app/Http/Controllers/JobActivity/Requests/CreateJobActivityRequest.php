<?php

namespace App\Http\Controllers\Jobs\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateJobRequest extends FormRequest
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
            'creator' => 'nullable',
           "created_at"=>"nullable",
           "activity_id"=>"nullable",
           "comment"=>"nullable"
        ];
    }
}
