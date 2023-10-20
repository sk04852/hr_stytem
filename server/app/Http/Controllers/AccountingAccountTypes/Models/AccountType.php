<?php

namespace App\Http\Controllers\AccountingAccountTypes\Models;

use App\Models\BaseModel;

class AccountType extends BaseModel
{
	protected $table = 'account_types';

	protected $fillable = [
		'id',
		'account_type',
		'detail',
	];

	public function accountTypes($request)
    {
		return $this->when( !empty($request->account_type), function($query) use($request) {
			return $query->where('account_type', 'like','%'.$request->account_type.'%');
		});
    }

}
