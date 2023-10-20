<?php

namespace App\Http\Controllers\Transactions\Models;

use App\Http\Controllers\Invoices\Models\Invoice;
use App\Http\Controllers\TransactionTypes\Models\TransactionType;
use App\Models\BaseModel;
use Exception;

class Transaction extends BaseModel
{	
	protected $table = 'transactions';
	public static $snakeAttributes = false;	

	protected $fillable = [
		'module_id',
		'relation_id',
		'transaction_id',
		'transaction_type_id',
		'debit',
		'credit',
		'note',
		'company_id',
		'account_id'
	];

	public function transactionType(){
		return $this->belongsTo(TransactionType::class, 'transaction_type_id');
	}

}
