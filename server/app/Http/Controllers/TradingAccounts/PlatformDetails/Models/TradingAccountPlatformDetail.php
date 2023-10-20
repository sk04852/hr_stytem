<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Controllers\TradingAccounts\PlatformDetails\Models;
use App\Models\BaseModel;

class TradingAccountPlatformDetail extends BaseModel
{
	protected $table = 'trading_account_platform_details';
	public static $snakeAttributes = false;

	protected $casts = [
		'trading_account_id' => 'int',
		'balance' => 'float',
		'credit' => 'float',
		'equity' => 'float',
		'margin' => 'float',
		'margin_free' => 'float',
		'closed_pnl' => 'float',
		'open_pnl' => 'float'
	];

	protected $dates = [
		'last_login'
	];

	protected $fillable = [
		'trading_account_id',
		'last_login',
		'leverage',
		'balance',
		'credit',
		'equity',
		'margin',
		'margin_level',
		'margin_free',
		'closed_pnl',
		'open_pnl',
		'trading_platform_response'
	];

	public function tradingAccount()
	{
		return $this->belongsTo(TradingAccount::class, 'trading_platform_account_id');
	}
}
