<?php

namespace App\Http\Controllers\Estimates\Models;

use App\Http\Controllers\Clients\Models\Client;
use App\Http\Controllers\Companies\Models\Company;
use App\Http\Controllers\Estimates\Models\EstimateProduct;
use App\Http\Controllers\VendorProducts\Models\VendorProduct;
use App\Models\BaseModel;

class Estimate extends BaseModel
{
	protected $table = 'estimates';

	protected $fillable = [
        
		'estimate_date',
        'estimate_number',
		'expiry_date',
		'sales_person',
        'subject',
		'sub_total',
		'discount',
		'shipping_charges',
		'notes',
		'terms',
		'status',
        'balance',
		'amount_paid',
		'client_id',
        'company_id'

	];

	public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
	}

	public function product()
    {
        return $this->belongsTo(VendorProduct::class, 'product_id');
	}

	public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
	}

	public function products()
    {
        return $this->hasMany(EstimateProduct::class, 'invoice_id');
	}
}
