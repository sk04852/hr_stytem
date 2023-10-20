<?php

namespace App\Http\Controllers\Currencies\Models;
use App\Models\BaseModel;

class Currency extends BaseModel
{
	protected $table = 'currencies';
	public static $snakeAttributes = false;

	protected $fillable = [
		'company_id',
		'name',
		'currency_code'
	];

	public function comapny() {
		$this->belongsTo(Companies::class,'company_id');
	}

}
