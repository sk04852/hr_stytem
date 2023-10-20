<?php

namespace App\Http\Controllers\Companies\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCompanyRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $available_locale = config('translatable.locales');

        $rules = collect([
            'invoicing_info' => 'required|max:500',
            'address' => 'nullable|max:500',
            'vat' => 'nullable|max:191',
            'industry_names' => 'nullable|array',
            'industry_names.*' => 'max:100',
            'files' => 'nullable|array',
            'files.*' => 'file'
        ]);

        /**
         * Most Important, all allowed languages should in translatable config file.
         */
        if(is_array($available_locale) && !empty($available_locale)){
            foreach ($available_locale as $locale){
                $rules = $rules->merge([
                    $locale => ['array'],
                    $locale.'.company_name'=> ['required', 'max:255', Rule::unique('companies', 'name')->where('locale', $locale)->whereNull('deleted_at')],
                    $locale.'.contacts' => 'required|array',
                    $locale.'.contacts.*.name'=> ['required', 'max:255'],
                    $locale.'.contacts.*.email'=> 'required|max:255',
                    $locale.'.contacts.*.phone'=> 'required|max:255',
                    $locale.'.contacts.*.position'=> 'nullable|max:255',
                    $locale.'.location' => 'nullable|array',
                    $locale.'.location.*.location' => 'max:255',
                ]);
            }
        }

        return $rules->all();
    }
}
