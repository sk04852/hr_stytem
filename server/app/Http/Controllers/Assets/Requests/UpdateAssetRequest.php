<?php

namespace App\Http\Controllers\Assets\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [

            'id' => ['required', 'integer' ,'exists:assets,id'],
            'asset_name' => ['required', 'min:3', 'max:150'],
            'asset_type_id' => ['required', 'exists:generic_asset_types,id'],
            'location_id' => ['required', 'exists:generic_locations,id'],
            'serial_number' => ['required', 'min:2', 'max:40'],
            'description' => ['nullable', 'min:0', 'max:500'],
            'colour' => ['nullable'],

        ];
    }

}
