<?php

namespace App\Http\Controllers\Estimates\Models;

use App\Http\Controllers\VendorProducts\Models\VendorProduct;
use App\Models\BaseModel;

class EstimateProduct extends BaseModel
{
	protected $table = 'estimate_products';

	protected $fillable = [
        
		'pre_order_id',
		'product_id',
		'product_name',
		'price',
		'quantity',
		'discount',
        'total',
		'sub_total',
		'note',
	];

	public function product()
    {
        return $this->belongsTo(VendorProduct::class, 'product_id');
	}

}
