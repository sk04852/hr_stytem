<?php

namespace App\Http\Controllers\Companies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyPr extends Model
{
    use SoftDeletes;

    protected $table = 'companies_pr';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'invoicing_info',
        'address',
        'vat',
        'industry_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function industries(){
        return $this->belongsToMany(CompanyIndustry::class, 'companies_industries','company_pr_id','industry_id');
    }

    public function company (){
        return $this->hasMany('App\Http\Controllers\Companies\Models\Company', 'company_pr_id', 'id');
    }

    public function companyContacts (){
        return $this->hasMany('App\Http\Controllers\Companies\Models\CompanyContact', 'company_pr_id', 'id');
    }

    public function companylocations (){
        return $this->hasMany('App\Http\Controllers\Companies\Models\CompanyLocation', 'company_pr_id', 'id');
    }

    public function companyFiles (){
        return $this->hasMany('App\Http\Controllers\Companies\Models\CompanyFile', 'company_pr_id', 'id');
    }

    public function jobs(){
        return $this->hasMany('App\Http\Controllers\Jobs\Models\JobPr', 'company_pr_id', 'id');
    }

    public function timeline (){
        return $this->morphMany('App\Http\Controllers\Timelines\Models\Timeline', 'model');
    }

    /**
     * Get all auto generate urls.
     */
    public function autoGenerateURL()
    {
        return $this->morphMany('App\Models\AutoGenerateURL', 'model');
    }

}
