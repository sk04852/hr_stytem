<?php

namespace App\Http\Controllers\Companies\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCompanyContactRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $available_locale = config('translatable.locales');

        $rules = collect([]);

        /**
         * Most Important, all allowed languages should in translatable config file.
         */
        if(is_array($available_locale) && !empty($available_locale)){
            foreach ($available_locale as $locale){
                $rules = $rules->merge([
                    $locale => ['array'],
                    $locale.'.contacts' => 'required|array:name,email,phone,position',
                    $locale.'.contacts.name'=> ['required', 'max:255'],
                    $locale.'.contacts.email'=> 'required|max:255',
                    $locale.'.contacts.phone'=> 'required|max:255',
                    $locale.'.contacts.position'=> 'nullable|max:255'
                ]);
            }
        }

        return $rules->all();
    }
}
