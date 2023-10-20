<?php

namespace App\Http\Controllers\Accounts\Models;

use App\Models\BaseModel;
use App\Models\Account;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountLegalInformation extends BaseModel
{
    use SoftDeletes;

    protected $table = 'account_legal_information';
    public static $snakeAttributes = false;

    protected $casts = [
        'account_id' => 'int'
    ];

    protected $fillable = [
        'account_id',
        'politically_exposed',
        'annual_income',
        'net_worth',
        'purpose_of_investment',
        'estimated_deposit_amount',
        'risk_level',
        'sole_beneficiary_of_account',
        'accepted_terms_conditions',
        'accepted_privacy_policy',
        'accepted_mandatory_information',
        'accepted_order_execution_policy',
        'risk_reward_profile_investing',
        'accepted_risk_disclosure',
        'accepted_statement',
        'accepted_client_agreement',
        'accepted_cookies_policy',
        'cookies_policy',
        'accepted_client_categorization_policy',
        'accepted_conflict_of_interest_policy',
        'accepted_complaint_handling_procedure',
        'accepted_investor_compensation_fund',
        'appropriateness_risk_warning',
        'average_yearly_net_disposable_income',
        'net_disposable_income',
        'soecify_name_of_institution',
        'country_of_origin',
        'risk_reward_profile_in_crm',
        'withdrawal_destination_different_from_funding_origin',
        'what_are_your_investment_objectives',
        'investment_objectives',
        'amount_annual_income',
        'years_of_work',
        'investment_awareness',
        'expected_activity_of_account',
        'own_real_estate',
        'location_of_real_estate',
        'preferred_method_for_receving_updates',
        'expected_fund_method',
        'net_worth_detail',
        'estimated_amount_detail',
        'other_source_of_income',
        'other_source_of_income',
        'deposit_country',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
