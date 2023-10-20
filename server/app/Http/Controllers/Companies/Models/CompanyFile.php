<?php

namespace App\Http\Controllers\Companies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyFile extends Model
{
    use SoftDeletes;

    protected $table = 'company_attachments';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'company_pr_id',
        'file_name',
        'path'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function companyPr(){
        return $this->belongsTo('App\Http\Controllers\Companies\Models\CompanyPr', 'company_pr_id', 'id');
    }

    public function getPathAttribute($value)
    {
        return asset('storage/'.$value);
    }

}
