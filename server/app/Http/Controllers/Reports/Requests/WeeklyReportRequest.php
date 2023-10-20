<?php

namespace App\Http\Controllers\Reports\Requests;
use Illuminate\Foundation\Http\FormRequest;

class WeeklyReportRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [

            'customer_id' => ['required','exists:generic_customers,id'],
        ];
    }


}
