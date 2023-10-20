<?php

namespace App\Http\Controllers\Suppliers\Models;
use App\Models\BaseModel;

class Supplier extends BaseModel
{
	protected $table = 'suppliers';
	public static $snakeAttributes = false;

	protected $fillable = [
        'company_id',
        'created_by',
        'name',
        'contact_person_name',
        'primary_email',
        'secondary_email',
        'phone',
        'mobile',
        'fax',
        'city',
        'country',
        'address_1',
        'address_2'
    ];

    public function suppliersFilter($request)
    {
       return Supplier::when(!empty($request->name), function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->name. '%');
        })
        ->when(!empty($request->contact_person_name), function ($query) use ($request) {
            return $query->where('contact_person_name', 'like', '%' . $request->contact_person_name. '%');
        })
        ->when(!empty($request->city), function ($query) use ($request) {
            return $query->where('city', 'like', '%' . $request->city. '%');
        })
        ->when(!empty($request->primary_email), function ($query) use ($request) {
            return $query->where('primary_email', 'like', '%' . $request->primary_email. '%');
        })
        ->when(!empty($request->country), function ($query) use ($request) {
            return $query->where('country', 'like', '%' . $request->country. '%');
        });
    }

    public function scopeWithCompany($query) {
        return $query->with(['company' => function($query){ $query->select(['id','name']);}]);
    }


}
