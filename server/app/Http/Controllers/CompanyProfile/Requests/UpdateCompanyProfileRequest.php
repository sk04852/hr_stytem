<?php

namespace App\Http\Controllers\CompanyProfile\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyProfileRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', 'exists:company-pr,id'],
            'companypr-ID' => ['nullable', 'max:255'],
            'company-ID' => ['nullable', 'exists:company,id'],
            'Invocing-info' => ['nullable'],
            'Doc-ID' => ['nullable'],
            'Docunment' => ['nullable'],
        ];
    }
}
