<?php

namespace App\Http\Controllers\AccountingTransactionAccounts\Models;

use App\Models\BaseModel;

class AccountingTransactionAccount extends BaseModel
{
	protected $table = 'transaction_accounts';

	protected $fillable = [
		'id',
		'company_id',
		'account_type_id',
		'account_number',
		'balance'
	];

}
