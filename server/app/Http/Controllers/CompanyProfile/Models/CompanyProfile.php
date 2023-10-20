<?php

namespace App\Http\Controllers\CompanyProfile\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $table = 'company-pr';
    public static $snakeAttributes = false;
    public $timestamps = false;

    protected $fillable = [
        'companypr-ID',
        'company-ID',
        'Invocing-info',
        'Doc-ID',
        'Docunment',
    ];

    public function CompanyProfilesFilter($request){

        return CompanyProfile::when(!empty($request->company_id), function ($query) use ($request) {
            return $query->where('company-ID', '=', $request->company_id);
        })
        ->when(!empty($request->companypr->ID), function ($query) use ($request) {
            $query->where('companypr-ID', $request->companypr->ID);
        });
    }

}
