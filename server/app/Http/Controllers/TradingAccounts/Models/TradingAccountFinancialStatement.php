<?php
namespace App\Http\Controllers\TradingAccounts\Models;
use App\Models\BaseModel;
use Carbon\Carbon;
class TradingAccountFinancialStatement extends BaseModel
{
	protected $table = 'trading_account_financial_statements';
	public static $snakeAttributes = false;

	protected $casts = [
		'amount' => 'float',
		'depositor' => 'int',
		'usd_amount' => 'float',
		'eur_amount' => 'float',
		'usd_conversation_rate' => 'float',
		'eur_conversation_rate' => 'float',
		'is_ftd' => 'bool',
		'assigned_to' => 'int',
		'requested_by' => 'int'
	];

	protected $dates = [
		'deposit_date'
	];

	protected $fillable = [
		'type',
		'type_description',
		'amount',
		'currency',
		'status',
		'transaction_id',
		'transaction_details',
		'transaction_response',
		'deposit_date',
		'depositor',
		'usd_amount',
		'eur_amount',
		'usd_conversation_rate',
		'eur_conversation_rate',
		'is_ftd',
		'country',
		'lead_source',
		'psp_instance_id',
		'transaction_comment',
		'affiliate_id',
		'assigned_to',
		'unique_id',
		'source',
		'gateway_name',
		'payment_method',
		'credit_card_last_4_digits',
		'trading_platform_response',
		'gateway_instance',
		'requested_by'
	];
}
