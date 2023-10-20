<?php

namespace App\Http\Controllers\Companies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $table = 'companies';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'company_pr_id',
        'locale',
        'name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Scope a query to only include Locale.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLocale($query)
    {
        return $query->where('locale', '=', app()->getLocale());
    }

    public function companyPr(){
        return $this->belongsTo('App\Http\Controllers\Companies\Models\CompanyPr', 'company_pr_id', 'id');
    }

}
