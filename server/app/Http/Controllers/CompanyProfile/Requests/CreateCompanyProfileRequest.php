<?php

namespace App\Http\Controllers\CompanyProfile\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCompanyProfileRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'companypr-ID' => ['nullable', 'max:255'],
            'company-ID' => ['nullable', 'exists:company,id'],
            'Invocing-info' => ['nullable'],
            'Doc-ID' => ['nullable'],
            'Docunment' => ['nullable'],
        ];
    }
}
