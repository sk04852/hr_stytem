<?php

namespace App\Http\Controllers\Assets\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAssetRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [

            'asset_name' => ['required', 'min:3', 'max:150'],
            'asset_type_id' => ['required', 'exists:generic_asset_types,id'],
            'location_id' => ['required', 'exists:generic_locations,id'],
            'serial_number' => ['required', 'min:2', 'max:40' ,Rule::unique('assets')->where(function($query) {
                $query->where('company_id', '=', auth()->user()->company_id)->where('serial_number', $this->serial_number)->where('asset_name', $this->asset_name);
             })],
            'description' => ['nullable', 'min:0', 'max:500'],
            'colour' => ['nullable'],

        ];
    }

}
