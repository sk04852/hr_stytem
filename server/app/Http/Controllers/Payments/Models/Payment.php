<?php

namespace App\Http\Controllers\Payments\Models;

use App\Http\Controllers\Clients\Models\Client;
use App\Http\Controllers\Companies\Models\Company;
use App\Http\Controllers\Outlets\Models\Outlet;
use App\Models\BaseModel;

class Payment extends BaseModel
{
    protected $table = 'payments';
    public static $snakeAttributes = false;

    protected $fillable = [
        'client_id',
        'company_id',
        'outlet_id',
        'payment_type',
        'amount',
        'recieved_by',
        'notes',
        'payment_date'
    ];

    public function paymentFilters($request)
    {
        return Payment::when(!empty($request->payment_type), function ($query) use ($request) {
			$query->where('payment_type', 'like', '%' . $request->payment_type . '%');
        })
		->when(!empty($request->client_first_name), function ($query) use ($request) {
            $query->whereHas('client', function ($query) use ($request) {
                $query->where('first_name', 'like', '%' . $request->client_name . '%');
            });
		})
		->when(!empty($request->client_email), function ($query) use ($request) {
            $query->whereHas('client', function ($query) use ($request) {
                $query->where('email', 'like', '%' . $request->client_email . '%');
            });
		})
        ->when(!empty($request->client_id), function ($query) use ($request) {
            $query->whereHas('client', function ($query) use ($request) {
                $query->where('id', '=', $request->client_id);
            });
		})
		->when(!empty($request->date_from), function ($query) use ($request) {
            return  $query->whereDate('created_at', '>=', $request->date_from);
        })
        ->when(!empty($request->date_until), function ($query) use ($request) {
            return  $query->whereDate('created_at', '<=', $request->date_until);
        }); 
    }

	public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
	}

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
	}

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
	}
}
