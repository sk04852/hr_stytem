<?php

namespace App\Http\Controllers\Generics\Models;

use App\Http\Controllers\VendorProducts\Models\VendorProduct;
use App\Models\BaseModel;

class VatCode extends BaseModel implements IGeneric
{
    protected $table = 'generic_vat_codes';

    protected $fillable = [
        'company_id',
		'name',
		'param_1',
        'param_2'
    ];

    public function product()
    {
        return $this->hasOne(VendorProduct::class);
    }
}
