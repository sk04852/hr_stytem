<?php

namespace App\Http\Controllers\Generics\Models;

use App\Http\Controllers\Invoices\Models\Invoice;
use App\Models\BaseModel;

class Customer extends BaseModel implements IGeneric
{
    protected $table = 'generic_customers';

    protected $fillable = [
        'company_id',
		'name',
		'param_1',
        'param_2'
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
