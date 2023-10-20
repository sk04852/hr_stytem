<?php

namespace App\Http\Controllers\Clients\Models;

use App\Http\Controllers\Estimates\Models\Estimate;
use App\Http\Controllers\Invoices\Models\Invoice;
use App\Http\Controllers\Payments\Models\Payment;
use App\Models\BaseModel;

class Client extends BaseModel
{

    protected $table = 'clients';

    protected $fillable = [

        'id',
        'type',
        'first_name',
        'middle_name',
        'last_name',
        'currency',
        'company_name',
        'username',
        'password',
        'email',
        'phone',
        'mobile',
        'designation',
        'department',
        'website',
        'city',
        'zip',
        'state',
        'status',
        'code',
        'total_balance',
        // Other details
        'outstanding_receivables',
        'unused_credits',
        'enable_portal_access',
        'facebook',
        'twitter',
        'company_id',
        'created_by',
        'uuid'
    ];


    public function clientsFilter($request)
    {
       return Client::when(!empty($request->first_name), function ($query) use ($request) {
            return $query->where('first_name', 'like', '%' . $request->first_name. '%');
        })
        ->when(!empty($request->last_name), function ($query) use ($request) {
            return $query->where('last_name', 'like', '%' . $request->last_name. '%');
        })
        ->when(!empty($request->username), function ($query) use ($request) {
            return $query->where('username', 'like', '%' . $request->username. '%');
        })
        ->when(!empty($request->email), function ($query) use ($request) {
            return $query->where('email', 'like', '%' . $request->email. '%');
        })
        ->when(!empty($request->city), function ($query) use ($request) {
            return $query->where('city', 'like', '%' . $request->city. '%');
        });
    }

    public function toDigest() {
        return $this->select(['id', 'code', 'first_name', 'last_name'])->where('company_id', auth()->user()->id)->get();
	}

    public function address() {
        return $this->hasMany(ClientAddress::class);
    }

    public function addresses() {
        return $this->hasMany(ClientAddress::class)->latest();
    }

    public function scopeWithAddresses($query) {
        return $query->with(['addresses' => function($query){ $query->select(['id','type', 'client_id']);}]);
	}

    public function payments() {
        return $this->hasMany(Payment::class);
    }

    public function invoices() {
        return $this->hasMany(Invoice::class);
    }

    public function estimates() {
        return $this->hasMany(Estimate::class);
    }

    public function productPrices() {
        return $this->hasMany(ClientProductPrice::class);
    }

}
