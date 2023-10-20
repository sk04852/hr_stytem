<?php

namespace App\Http\Controllers\Generics\Models;

use App\Http\Controllers\IntakeStocks\Models\IntakeStock;
use App\Models\BaseModel;

class UsageUnit extends BaseModel implements IGeneric
{
	protected $table = 'generic_usage_units';
	
	protected $fillable = [
        'company_id',
		'name',
		'param_1',
        'param_2'
	];
	
	public function stocks()
    {
        return $this->hasMany(IntakeStock::class);
    }
}
