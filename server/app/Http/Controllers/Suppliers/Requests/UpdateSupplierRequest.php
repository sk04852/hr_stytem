<?php

namespace App\Http\Controllers\Suppliers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required','exists:suppliers,id'],
            'name' => ['required'],
            'primary_email' => [
                'required',
                'email',
                Rule::unique('suppliers', 'primary_email')->ignore($this->id)
            ]
        ];
    }

}
