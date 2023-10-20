<?php

namespace App\Http\Controllers\PloiciesCategories\Models;

use App\Models\BaseModel;


class PoliciesCategory extends BaseModel
{
	protected $table = 'policies_categories';
	public static $snakeAttributes = false;

	protected $fillable = [
		'category_name'
	];

}
