<?php

namespace App\Http\Controllers\ImportData\Requests;

use App\Http\Controllers\Documents\Requests\UploadMediaRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ImportDataRequest extends UploadMediaRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "file" => "required|mimes:jpeg,bmp,png,xls,xlsx,txt,csv,doc,docx,pdf|max:200000000",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
