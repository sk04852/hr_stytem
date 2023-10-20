<?php

namespace App\Http\Controllers\Jobs\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class DeleteJobVideoRequest extends FormRequest
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
            'job_video_id' => ['required', Rule::exists('jobs_videos', 'id')->whereNull('deleted_at')]
        ];
    }
}
