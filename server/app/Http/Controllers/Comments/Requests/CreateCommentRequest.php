<?php

namespace App\Http\Controllers\Comments\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateCommentRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "module" => "required",
            "is_mass_comment" => "nullable",
            "relation_id" => "required_without:relation_ids",
            "relation_ids" => "required_without:relation_id|array",
            "comment" => "required",
            "is_reply" => "required",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
