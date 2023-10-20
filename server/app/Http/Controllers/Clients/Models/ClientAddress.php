<?php

namespace App\Http\Controllers\Clients\Models;

use App\Models\BaseModel;

class ClientAddress extends BaseModel
{    
    protected $table = 'client_addresses';
    protected $primaryKey = 'id';
    protected $fillable = [
        'client_id',
        'type',
        'attention',
        'country',
        'address_1',
        'address_2',
        'city',
        'state',
        'zip_code',
        'phone',
        'fax',
    ];

    public function clientAddressesFilter($request)
    {
       return ClientAddress::when(!empty($request->client_id), function ($query) use ($request) {
            return $query->where('client_id',  $request->client_id);
        })
        ->when(!empty($request->type), function ($query) use ($request) {
            return $query->where('type', 'like', '%' . $request->type. '%');
        })
        ->when(!empty($request->country), function ($query) use ($request) {
            return $query->where('country', 'like', '%' . $request->country. '%');
        })
        ->when(!empty($request->state), function ($query) use ($request) {
            return $query->where('state', 'like', '%' . $request->state. '%');
        })
        ->when(!empty($request->city), function ($query) use ($request) {
            return $query->where('city', 'like', '%' . $request->city. '%');
        })
        ->when(!empty($request->phone), function ($query) use ($request) {
            return $query->where('phone', $request->phone);
        })
        ->when(!empty($request->zip_code), function ($query) use ($request) {
            return $query->where('zip_code', $request->zip_code);
        })
        ->when(!empty($request->city), function ($query) use ($request) {
            return $query->where('city', 'like', '%' . $request->city. '%');
        });
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }
   
}
