<?php

namespace App\Http\Controllers\Suppliers\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSupplierRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required'],
            'primary_email' => ['nullable','unique:suppliers,primary_email'],
        ];
    }


}
