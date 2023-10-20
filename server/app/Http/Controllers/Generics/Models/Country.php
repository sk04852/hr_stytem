<?php

namespace App\Http\Controllers\Generics\Models;


use App\Models\BaseModel;

class Country extends BaseModel implements IGeneric
{
	protected $table = 'countries';
	public $timestamps = false;
	protected $fillable = [
		'name',
		'sort_name',
		'phone_code',
	];
}
