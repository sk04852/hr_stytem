<?php

namespace App\Http\Controllers\Mail\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendCompanyMailRequest extends FormRequest
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
            'company_contact_id' => ['required', Rule::exists('company_contacts', 'id')->whereNull('deleted_at')],
            'auto_generated_url_id' => ['required', Rule::exists('auto_generate_urls', 'id')->where('status', 'valid')],
            'from' => 'required|email',
            'subject' => "required",
            'body' => "required",            
        ];
    }
}
