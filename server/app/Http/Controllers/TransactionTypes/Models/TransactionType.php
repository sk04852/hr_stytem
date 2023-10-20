<?php

namespace App\Http\Controllers\TransactionTypes\Models;

use App\Http\Controllers\Options\Models\FieldOption;
use App\Models\BaseModel;

class TransactionType extends BaseModel
{
	protected $table = 'transaction_types';
	public static $snakeAttributes = false;

	protected $fillable = [
		'name',
		'type',
		'action',
		'function',
		'comment'
	];

	public function type()
	{
		return  $this->belongsTo(FieldOption::class, 'type');
	}
}
