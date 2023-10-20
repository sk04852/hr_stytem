<?php

namespace App\Http\Controllers\Invoices\Models;

use App\Http\Controllers\VendorProducts\Models\VendorProduct;
use App\Models\BaseModel;

class InvoiceProduct extends BaseModel
{
	protected $table = 'invoice_products';

	protected $fillable = [
        
		'invoice_id',
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
