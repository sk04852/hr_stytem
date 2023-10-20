<?php

namespace App\Http\Controllers\Brands\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Models\BaseModel;
use App\Http\Controllers\Users\Models\User;

class Brand extends BaseModel
{
    protected $table = 'brands';
    public static $snakeAttributes = false;

    protected $fillable = [
        'company_id',
        'status',
        'name',
        'primary_email',
        'secondary_email',
        'website',
        'primary_phone',
        'secondary_phone',
        'fax',
        'employees',
        'industry_id',
        'type',
        'assigned_to',
        'billing_country',
        'billing_address',
        'billing_po_box',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'shipping_address',
        'shipping_country',
        'shipping_po_box',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'description',
        'created_by',
        'logo'
    ];

    /* Filter Function */
    public function scopeFilter($query, array $filters)
    {
        if (array_key_exists('name', $filters)) {
            $query->where('name', $filters['name']);
        }
        if (array_key_exists('primary_email', $filters)) {
            $query->where('primary_email', $filters['primary_email']);
        }
        if (array_key_exists('website', $filters)) {
            $query->where('website', $filters['website']);
        }
        if (array_key_exists('primary_phone', $filters)) {
            $query->where('primary_phone', $filters['primary_phone']);
        }
        if (array_key_exists('employees', $filters)) {
            $query->where('employees', $filters['employees']);
        }
        if (array_key_exists('industry_id', $filters)) {
            $query->where('industry_id', $filters['industry_id']);
        }
        if (array_key_exists('type', $filters)) {
            $query->where('type', $filters['type']);
        }
        if (array_key_exists('assigned_to', $filters)) {
            $query->where('assigned_to', $filters['assigned_to']);
        }
        if (array_key_exists('billing_country', $filters)) {
            $query->where('billing_country', $filters['billing_country']);
        }
        if (array_key_exists('billing_city', $filters)) {
            $query->where('billing_city', $filters['billing_city']);
        }
        if (array_key_exists('billing_state', $filters)) {
            $query->where('billing_state', $filters['billing_state']);
        }
        if (array_key_exists('shipping_country', $filters)) {
            $query->where('shipping_country', $filters['shipping_country']);
        }
        if (array_key_exists('shipping_city', $filters)) {
            $query->where('shipping_city', $filters['shipping_city']);
        }
        if (array_key_exists('shipping_state', $filters)) {
            $query->where('shipping_state', $filters['shipping_state']);
        }
        if (array_key_exists('created_by', $filters)) {
            $query->where('created_by', $filters['created_by']);
        }
    }

    /* Auto Start */
    public function generalSettings()
    {
        return $this->hasMany(GeneralSetting::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_brand')
            ->withPivot('is_default');
    }

    /* Auto End */

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        return $this->name = $name;
    }

    public function toDigest()
    {
        return $this->select(['id', 'name', 'primary_email', 'assigned_to', 'created_by'])->get();
    }
}
