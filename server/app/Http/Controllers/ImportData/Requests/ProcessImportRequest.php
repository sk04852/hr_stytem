<?php

namespace App\Http\Controllers\ImportData\Requests;

use App\Http\Controllers\Documents\Requests\UploadMediaRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ProcessImportRequest extends UploadMediaRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "file_id" => "required|exists:csv_data,id",
            "module_id"=> "required|exists:modules,id"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
