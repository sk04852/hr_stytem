<?php

namespace App\Http\Controllers\Companies\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $available_locale = config('translatable.locales');

        $rules = collect([
            'company_pr_id' => 'required|exists:companies_pr,id',
            'invoicing_info' => 'required|max:500',
            'address' => 'nullable|max:500',
            'vat' => 'nullable|max:191',
            'industries' => 'nullable|array',
            'industries.*.id' => 'nullable|exists:company_industries,id',
            'industries.*.name' => 'max:100',
            'files' => 'nullable|array',
            'files.*' => 'file',
            'old_files' => 'nullable|array',
            'old_files.*.id' => 'nullable|exists:company_attachments,id'
        ]);

        /**
         * Most Important, all allowed languages should in translatable config file.
         */
        if(is_array($available_locale) && !empty($available_locale)){
            foreach ($available_locale as $locale){
                $rules = $rules->merge([
                    $locale.'.company_id'=> 'required|exists:companies,id',
                    $locale.'.company_name'=> ['required', 'max:255' , Rule::unique('companies', 'name')->where('locale', $locale)->ignore($this->$locale['company_id'])->whereNull('deleted_at')],
                    $locale.'.contacts' => 'required|array',
                    $locale.'.contacts.*.id'=> 'nullable|exists:company_contacts,id',
                    $locale.'.contacts.*.name'=> 'required|max:255',
                    $locale.'.contacts.*.email'=> 'required|max:255',
                    $locale.'.contacts.*.phone'=> 'required|max:255',
                    $locale.'.contacts.*.position'=> 'nullable|max:255',
                    $locale.'.location' => 'nullable|array',
                    $locale.'.location.*.id' => 'nullable|exists:company_locations,id',
                    $locale.'.location.*.location' => 'max:255',
                ]);
            }
        }

        return $rules->all();
    }
}
