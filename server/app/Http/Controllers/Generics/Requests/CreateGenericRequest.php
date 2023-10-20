<?php

namespace App\Http\Controllers\Generics\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\GetModelName;
use Illuminate\Validation\Rule;

class CreateGenericRequest extends FormRequest
{

    use GetModelName;
    
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $table = $this->route()->parameter('type');

        return [

            'name' => ['required','string','max:150',Rule::unique('generic_'.$table)->where(function($query) {
                $query->where('company_id', '=', auth()->user()->company_id);
             })],
          
            'param_1' => ['nullable','string'],
            'param_2' => ['nullable','string'],
        ];
    }

    public function messages(){
        $type = $this->route()->parameter('type');

        return [
            'name.required' => 'The '.$this->getModelName($type) .' field is required',
            'name.unique' => 'The '.$this->getModelName($type) .' already exists in the system',
        ];
    }

   
}
