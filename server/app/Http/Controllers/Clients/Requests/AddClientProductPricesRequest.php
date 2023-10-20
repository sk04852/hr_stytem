<?php

namespace App\Http\Controllers\Clients\Requests;
use Illuminate\Foundation\Http\FormRequest;
class AddClientProductPricesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'prices'=> ['required', 'array'],
            'prices.*.product_id'=> ['required'],
            'prices.*.price'=> ['required'],
        ];
    }

}
