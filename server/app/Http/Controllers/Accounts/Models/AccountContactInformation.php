<?php

namespace App\Http\Controllers\Accounts\Models;

use App\Models\BaseModel;
use App\Models\Account;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountContactInformation extends BaseModel
{
	use SoftDeletes;

	protected $table = 'client_account_contact_information';
	public static $snakeAttributes = false;

	protected $casts = [
		'account_id' => 'int'
	];

	protected $fillable = [
		'account_id',
		'primary_phone',
		'secondary_phone',
		'primary_email',
		'secondary_email',
		'prefix',
        'email_confirmed',
		'mobile',
		'address',
		'address_2',
		'city',
		'state',
		'postal_code',
		'country',
		'po_box',
		'skype_id',
        'phone',
        'zip'
	];

	public function account()
	{
		return $this->belongsTo(Account::class);
	}
}
