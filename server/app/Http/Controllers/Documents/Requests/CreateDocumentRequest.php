<?php

namespace App\Http\Controllers\Documents\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateDocumentRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $acceptableTypes = 'General File,CV,Report,Receipt,Invoice,Bill,Other,Unknown';
        return [
            "file" => "nullable|mimes:jpeg,bmp,png,xls,xlsx,txt,csv,doc,docx,pdf|max:20000",
            'type'  => 'required|in:'.$acceptableTypes,
            'assigned_to'  => 'nullable|exists:users,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
