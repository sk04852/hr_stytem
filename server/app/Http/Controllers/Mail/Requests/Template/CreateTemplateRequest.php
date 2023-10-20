<?php

namespace App\Http\Controllers\Mail\Requests\Template;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTemplateRequest extends FormRequest
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
            'title' => 'required|max:191',
            'template_key' => ['required','max:191', Rule::unique('mail_templates', 'template_key')->whereNull('deleted_at')],
            'subject' => 'nullable|max:191',
            'to' => 'nullable|max:191',
            'cc' => 'nullable|max:191',
            'body' => 'required|max:65535'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => __('validation.required'),
            'title.max' => __('validation.max'),
            'template_key.required' => __('validation.required'),
            'template_key.max' => __('validation.max'),
            'template_key.unique' => __('validation.unique'),
            'subject.required' => __('validation.required'),
            'subject.max' => __('validation.max'),
            'to.required' => __('validation.required'),
            'to.max' => __('validation.max'),
            'cc.required' => __('validation.required'),
            'cc.max' => __('validation.max'),
            'body.required' => __('validation.required'),
            'body.max' => __('validation.max'),
        ];
    }
}
