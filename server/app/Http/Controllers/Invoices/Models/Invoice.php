<?php

namespace App\Http\Controllers\Invoices\Models;

use App\Http\Controllers\Clients\Models\Client;
use App\Http\Controllers\Companies\Models\Company;
use App\Http\Controllers\CreditNotes\Models\CreditNote;
use App\Http\Controllers\VendorProducts\Models\VendorProduct;
use App\Models\BaseModel;

class Invoice extends BaseModel
{
	protected $table = 'invoices';

	protected $fillable = [
		'client_id',
		'type',
		'invoice_number',
		'order_no',
		'invoice_date',
		'delivery_date',
		'sales_person',
        'subject',
		'discount',
        'tax',
		'sub_total',
        'total',
		'shipping_charges',
		'notes',
		'terms_n_conditions',
		'customer_note',
		'status',
        'balance',
		'amount_paid',
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
        return $this->hasMany(InvoiceProduct::class);
	}
}
