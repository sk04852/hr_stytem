<?php

namespace App\Http\Controllers\Accounts\Models;

use App\Models\BaseModel;
use App\Models\Account;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPersonalInformation extends BaseModel
{
    use SoftDeletes;

	protected $table = 'account_personal_information';
	public static $snakeAttributes = false;

	protected $casts = [
		'account_id' => 'int',
		'us_citizen' => 'bool',
		'recieve_newsletters' => 'bool'
	];

	protected $dates = [
		'date_of_birth'
	];

	protected $fillable = [
		'account_id',
		'date_of_birth',
		'place_of_birth',
		'citizenship',
		'identification_number',
		'education_level',
		'occupation',
		'name_of_own_company',
		'business_profile',
		'spoken_language',
		'us_citizen',
		'recieve_newsletters',
		'source_of_income',
        'nationality',
        'professional_status',
		'expected_outgoing_country',
        'profession_or_industry',
        'tax_number',
        'position',
        'country_of_tax_residency',
        'country_of_birth',
        'field_of_study',
		'national_client_identifier',
        'country_of_tax_residency'
	];

	public function account()
	{
		return $this->belongsTo(Account::class);
    }

}
