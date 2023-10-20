<?php

namespace App\Http\Controllers\AssignAsset\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAssignAssetRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

            'employee_id' => ['required', 'exists:employees,id'],
            'asset_id' => ['required', 'exists:assets,id'],
            
        ];
    }

}
