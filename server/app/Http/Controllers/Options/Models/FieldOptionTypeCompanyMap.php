<?php

namespace App\Http\Controllers\Options\Models;
use App\Models\BaseModel;

class FieldOptionTypeCompanyMap extends BaseModel
{
	protected $table = 'field_options_types_company_map';
	public static $snakeAttributes = false;

	protected $fillable = [
        'company_id',
        'field_option_type_id'
    ];

	public function fieldOptionTypes()
	{
		return $this->hasMany(FieldOptionType::class, 'id', 'field_option_type_id');
	}
}
