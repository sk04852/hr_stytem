<?php

namespace App\Http\Controllers\MonetaryTransactions\Models;

use App\Models\BaseModel;

class FinancialTransactionExtraInfo extends BaseModel
{
	protected $table = 'financial_transactions_extra_info';

	protected $fillable = [
		'monetary_transaction',
		'payment_method',
		'gateway_instance',
		'gateway_name',
		'channel',
		'coupon',
	];
}
