<?php

namespace App\Http\Controllers\Mail\Requests\Template;

use Illuminate\Foundation\Http\FormRequest;

class SendMailRequest extends FormRequest
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
            'to' => "required",
            'from' => 'required|email',
            'subject' => "required",
            'body' => "required"
        ];
    }
}
