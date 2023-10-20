<?php

namespace App\Http\Controllers\Payments\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "id" => "required|exists:payments,id",
            "client_id" => "required|exists:clients,id",
            "payment_type" => "required", Rule::in(['Cash','Bank Transfer','Check','Balance']),
            "amount" => "required",
            "payment_date" => "required|date"
        ];
    }

}
