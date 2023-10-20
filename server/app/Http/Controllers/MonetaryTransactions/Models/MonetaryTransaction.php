<?php

namespace App\Http\Controllers\MonetaryTransactions\Models;

use App\Http\Controllers\TransactionTypes\Models\TransactionType;
use App\Http\Controllers\Users\Models\User;
use App\Models\BaseModel;

class MonetaryTransaction extends BaseModel
{
	protected $table = 'monetary_transactions';
	public static $snakeAttributes = false;

	protected $fillable = [
		'trading_account_id',
		'account_name',
		'transaction_type_id',
		'amount',
		'transaction_id',
		'date',
		'crm_comment',
		'assigned_to'
	];

	public function monetaryTransactionsFilters($request)
	{
		return $this->when(!empty($request->account_name), function ($query) use ($request) {
			return $query->where('account_name', 'like', '%' . $request->account_name . '%');
		})
			->when(!empty($request->transaction_id), function ($query) use ($request) {
				return $query->where('transaction_id', $request->transaction_id);
			})
			->when(!empty($request->amount), function ($query) use ($request) {
				return $query->where('amount', $request->amount);
			})
			->when(!empty($request->from_date), function ($query) use ($request) {
				return  $query->whereDate('date', '>=', $request->from_date);
			})
			->when(!empty($request->to_date), function ($query) use ($request) {
				return  $query->whereDate('date', '<=', $request->to_date);
			})
			->when(!empty($request->from), function ($query) use ($request) {
				return  $query->whereDate('created_at', '>=', $request->from);
			})
			->when(!empty($request->to), function ($query) use ($request) {
				return  $query->whereDate('created_at', '<=', $request->to);
			});
	}

	public function financialDetail()
	{
		return $this->hasOne(FinancialTransactionExtraInfo::class, 'monetary_transaction');
	}

	public function transactionType()
	{
		return $this->belongsTo(TransactionType::class, 'transaction_type_id');
	}

	public function assignedTo()
	{
		return $this->belongsTo(User::class, 'assigned_to');
	}
}
