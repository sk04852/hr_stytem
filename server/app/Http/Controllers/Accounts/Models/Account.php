<?php

namespace App\Http\Controllers\Accounts\Models;

use App\GlobalSeachRule;
use App\Http\Controllers\Brands\Models\Brand;
use App\Http\Controllers\Comments\Models\Comment;
use App\Http\Controllers\People\Interfaces\IPerson;
use App\Http\Controllers\People\Models\Person;
use App\Http\Controllers\Tickets\Models\Ticket;
use App\Http\Controllers\TradingAccounts\Models\TradingAccount;
use App\Http\Controllers\Users\Models\User;
use App\Http\Resources\Searchables\SearchableAccountResource;
use App\Interfaces\GloballySearchable;
use ScoutElastic\Searchable;
use App\Http\Controllers\Search\Indexers\AccounstIndexConfigurator;


class Account extends Person implements IPerson, GloballySearchable
{
    use Searchable;

    protected $indexConfigurator = AccounstIndexConfigurator::class;
    protected $mapping = [
        'properties' => [
            'text' => [
                'type' => 'text',
            ],
        ]
    ];

	protected $table = 'accounts';
	public static $snakeAttributes = false;
	protected $guard_name = 'api';

    protected $fillable = [
		'account_name',
        'user_id',
		'brand_id',
		'assigned_to',
		'account_description',
		'email_opt_out',
		'notify_owner',
		'ip',
        'ownership',
        'id_type',
        'introducing_broker',
        'member_of',
        'original_retention_owner',
        'verification_status',
        'provider_name',
        'addtional_information',
        'trading_status',
        'primary_tp_account',
        'account_enabled',
        'ftd_amount',
        'ftd_status',
        'ftd_date',
        'ftd_currency',
        'redeposit_status',
        'last_deposit_date',
        'ftd_owner',
        'secondary_income',
        'secondary_income_specify',
        'secondary_source_of_income',
        'anticipated_account_turnover_annually',
        'secondary_source_of_income_specify',
        'fund_method_country',
        'proof_of_residence',
        'proof_of_identity',
        'document_status',
		'account_type_requested',
		'website_language',
		'account_status',
		'recovery_question',
		'recovery_answer',
		'document_verified',
		'compliance_completed',
		'trading_disabled',
		'password_for_email',
		'client_category',
		'requested_leverage',
		'ib_id',
		'account_type',
		'client_type',
        'account_stage',
        'recover_question',
        'registration_country'
	];

	public function accountsFilters($request)
	{
		return $this->when(!empty($request->first_name), function ($query) use ($request) {
            $query->whereHas('user', function ($query) use ($request) {
                return $query->where('first_name',  'like', $request->first_name . '%');
             });
		})
			->when(!empty($request->last_name), function ($query) use ($request) {
                $query->whereHas('user', function ($query) use ($request) {
                    return $query->where('last_name',  'like', $request->last_name . '%');
                 });
			})
			->when(!empty($request->email), function ($query) use ($request) {
                $query->whereHas('user', function ($query) use ($request) {
				    return $query->where('email', 'like', '%' . $request->email . '%');
                });
			})
            ->when(!empty($request->account_name), function ($query) use ($request) {
				return $query->where('account_name', 'like', '%' . $request->account_name . '%');
			})
			->when(!empty($request->assigned_to), function ($query) use ($request) {
				return $query->where('assigned_to', $request->assigned_to);
			})
			->when(!empty($request->created_at), function ($query) use ($request) {
				return $query->where('created_at', $request->created_at);
			})
			->when(!empty($request->updated_at), function ($query) use ($request) {
				return $query->where('updated_at', $request->updated_at);
			})
			->when(!empty($request->alphabet), function ($query) use ($request) {
                $query->whereHas('user', function ($query) use ($request) {
                    return $query->where('last_name',  'like', $request->alphabet . '%');
                 });
			});
	}
	public function accountContactInformation()
	{
		return $this->hasOne(AccountContactInformation::class);
	}
	public function accountPersonalInformation()
	{
		return $this->hasOne(AccountPersonalInformation::class);
	}
	public function accountTradingExperience()
	{
		return $this->hasOne(AccountTradingExperience::class);
	}

	public function accountMarketingDetails()
	{
		return $this->hasOne(AccountMarketingDetail::class);
	}

	public function accountLegalInformation()
	{
		return $this->hasOne(AccountLegalInformation::class);
	}

	public function accountLeadConversion()
	{
		return $this->hasOne(AccountLeadConversion::class);
	}

	public function tickets()
	{
		return $this->hasMany(Ticket::class);
	}
	public function tradingAccounts()
	{
		return $this->hasMany(TradingAccount::class);
	}
	public function brand()
	{
		return $this->belongsTo(Brand::class)->select(['id', 'name']);
	}

    public function assignedTo()
	{
		return $this->belongsTo(User::class,'assigned_to')->select(['id', 'first_name', 'last_name', 'email']);
	}

    public function user()
	{
		return $this->belongsTo(User::class,'user_id');
	}

    public function comments()
    {
        return $this->hasMany(Comment::class,'module_id');
    }

	public function updateAccountContactDetails(AccountContactInformation $contactDetail, array $updateData)
	{
		$contactDetail->update($updateData);
        return $contactDetail;
	}

    public function updateUserDetails(User $userDetail, array $updateData)
	{
		$userDetail->update($updateData);
        return $userDetail;
	}

    public function saveUserDetails(User $userDetails)
	{
		$this->user()->save($userDetails);
	}

	public function saveAccountContactDetails(AccountContactInformation $contactDetail)
	{
		$this->accountContactInformation()->save($contactDetail);
	}

	public function updateAccountPersonalDetails(AccountPersonalInformation $personalDetail, array $updateData)
	{
		$personalDetail->update($updateData);
        return $personalDetail;
	}

	public function updateAccountMarketingDetails(AccountMarketingDetail $accountMarketingDetail, array $updateData)
	{
		$accountMarketingDetail->update($updateData);
        return $accountMarketingDetail;
	}


	public function saveAccountMarketingDetails(AccountMarketingDetail $accountMarketingDetail)
	{
		$this->accountMarketingDetails()->save($accountMarketingDetail);
	}

	public function updateAccountLegalInformation(AccountLegalInformation $accountLegalInformation, array $updateData)
	{
		$accountLegalInformation->update($updateData);
        return $accountLegalInformation;
	}

	public function saveAccountLegalInformation(AccountLegalInformation $accountLegalInformation)
	{
		$this->accountLegalInformation()->save($accountLegalInformation);
	}

	public function updateAccountLeadConversion(AccountLeadConversion $accountLeadConversion, array $updateData)
	{
		$accountLeadConversion->update($updateData);
        return $accountLeadConversion;
	}

	public function saveAccountLeadConversion(AccountLeadConversion $accountLeadConversion)
	{
		$this->accountLeadConversion()->save($accountLeadConversion);
	}

	public function saveAccountPersonalDetails(AccountPersonalInformation $personalDetail)
	{
		$this->accountPersonalInformation()->save($personalDetail);
	}


    public function saveAccountTradingExperience(AccountTradingExperience $accountTradingExperience)
	{
		$this->accountTradingExperience()->save($accountTradingExperience);
	}

	public function updateAccountTradingExperience(AccountTradingExperience $accountTradingExperience , array $updateData)
	{
		$accountTradingExperience->update($updateData);
        return $accountTradingExperience;
	}

    public function toSearchableArray()
    {
        $data = new SearchableAccountResource($this);
        return $data->toArray($this);
    }

    public function searchableAs()
    {
        return '_doc';
    }
}
