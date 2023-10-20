<?php

namespace App\Http\Controllers\Mail\Requests\Template;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteTemplateRequest extends FormRequest
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
            'id' => ['required', 'array'],
            'id.*' => [Rule::exists('mail_templates', 'id')->whereNull('deleted_at')],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => __('validation.required'),
            'id.array' => __('validation.array'),
            'id.*.exists' => __('validation.exists'),
        ];
    }
}
