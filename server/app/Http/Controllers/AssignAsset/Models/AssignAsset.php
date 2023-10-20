<?php

namespace App\Http\Controllers\AssignAsset\Models;

use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class AssignAsset extends BaseModel
{
	protected $table = 'assign_assets';

	protected $fillable = [
		'employee_id',
        'asset_id',
        'company_id',
        'status',
    ];
    
    public function checkAlreadyAssigned($data, $companyId){

        return AssignAsset::where('asset_id',$data['asset_id'])
        ->where('employee_id',$data['employee_id'])
        ->where('company_id',$companyId)
        ->where('status',1)
        ->exists();
    }

    public function UpdateAssetStatus($data, $companyId){

        return AssignAsset::where('asset_id',$data['asset_id'])
        ->where('company_id',$companyId)
        ->update(['status' => 0]);
    }

    public function listing($companyId)
    {
        return DB::table('assign_assets as aa')
        ->join('employees as e', 'e.id', 'aa.employee_id')
        ->join('assets as a', 'a.id', 'aa.asset_id')
        ->join('users as u', 'u.id', 'e.user_id')
        ->join('companies as c', 'c.id', 'a.company_id')
        ->select(['aa.id','u.first_name','u.last_name','u.email','c.company_name','a.asset_name', 'a.serial_number','aa.status'])
        ->where('aa.company_id', $companyId);
    } 
   

}
