<?php

namespace App\Http\Controllers\PromoCodes\Models;

use App\Http\Controllers\Brands\Models\Brand;
use App\Http\Controllers\Companies\Models\Company;
use App\Http\Controllers\Users\Models\User;
use App\Models\BaseModel;

class PromoCode extends BaseModel
{
    protected $table = 'promo_codes';

    protected $fillable = [

        'company_id',
        'status',
        'promo_code',
        'user_id',
        'brand_id',
        'company_id',

    ];

    /* Filter Function */
    public function scopeFilter($query, array $filters)
    {
        if (array_key_exists('promo_code', $filters)) {
            $query->where('promo_code', 'like', '%' . $filters['promo_code'] . '%');
        }
        if (array_key_exists('company_id', $filters)) {
            $query->where('company_id', $filters['company_id']);
        }
        if (array_key_exists('brand_id', $filters)) {
            $query->where('brand_id', $filters['brand_id']);
        }
        if (array_key_exists('user_id', $filters)) {
            $query->where('user_id', $filters['user_id']);
        }
        if (array_key_exists('status', $filters)) {
            $query->where('status', $filters['status']);
        }
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeWithCompany($query)
    {
        return $query->with(['company' => function ($query) {
            $query->select(['id', 'name']);
        }]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWithUser($query)
    {
        return $query->with(['user' => function ($query) {
            $query->select(['id', 'first_name', 'last_name']);
        }]);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function scopeWithBrand($query)
    {
        return $query->with(['brand' => function ($query) {
            $query->select(['id', 'name']);
        }]);
    }

    public function toDigest()
    {
        return $this->select(['id', 'promo_code'])->get();
    }
}
