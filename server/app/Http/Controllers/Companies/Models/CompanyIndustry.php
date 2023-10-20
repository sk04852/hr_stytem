<?php

namespace App\Http\Controllers\Companies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyIndustry extends Model
{
    use SoftDeletes;

    protected $table = 'company_industries';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function companiesPr (){
        return $this->belongsToMany(CompanyPr::class, 'companies_industries','industry_id', 'company_pr_id');
    }

}
