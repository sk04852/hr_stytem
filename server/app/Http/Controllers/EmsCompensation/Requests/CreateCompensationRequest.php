<?php

namespace App\Http\Controllers\EmsCompensation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCompensationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'amount'=> 'required|max:20',
            'currency'=>  ['required', Rule::in(['ALL',
            'AFN',
            'AED',
            'ARS',
            'AWG',
            'AUD',
            'AZN',
            'BSD',
            'BBD',
            'BDT',
            'BYR',
            'BZD',
            'BMD',
            'BOB',
            'BAM',
            'BWP',
            'BGN',
            'BRL',
            'BND',
            'KHR',
            'CAD',
            'KYD',
            'CLP',
            'CNY',
            'COP',
            'CRC',
            'HRK',
            'CUP',
            'CZK',
            'DKK',
            'DOP',
            'XCD',
            'EGP',
            'SVC',
            'EEK',
            'EUR',
            'FKP',
            'FJD',
            'GHC',
            'GIP',
            'GTQ',
            'GGP',
            'GYD',
            'HNL',
            'HKD',
            'HUF',
            'ISK',
            'INR',
            'IDR',
            'IRR',
            'IMP',
            'ILS',
            'JMD',
            'JPY',
            'JEP',
            'KZT',
            'KPW',
            'KRW',
            'KGS',
            'LAK',
            'LVL',
            'LBP',
            'LRD',
            'LTL',
            'MKD',
            'MYR',
            'MUR',
            'MXN',
            'MNT',
            'MZN',
            'NAD',
            'NPR',
            'ANG',
            'NZD',
            'NIO',
            'NGN',
            'NOK',
            'OMR',
            'PKR',
            'PAB',
            'PYG',
            'PEN',
            'PHP',
            'PLN',
            'QAR',
            'RON',
            'RUB',
            'SHP',
            'SAR',
            'RSD',
            'SCR',
            'SGD',
            'SBD',
            'SOS',
            'ZAR',
            'LKR',
            'SEK',
            'CHF',
            'SRD',
            'SYP',
            'TWD',
            'THB',
            'TTD',
            'TRY',
            'TRL',
            'TVD',
            'UAH',
            'GBP',
            'USD',
            'UYU',
            'UZS',
            'VEF',
            'VND',
            'YER',
            'ZWD'])],
            'frequency'=>  ['required', Rule::in(['Hourly','Weekly','Monthly','Yearly'])],
            'effective_date'=> ['required', 'date','after:today'],
            'end_date'=> ['nullable', 'date','after_or_equal:effective_date'],
            'note'=> ['nullable', 'string'],

        ];
    }
}
