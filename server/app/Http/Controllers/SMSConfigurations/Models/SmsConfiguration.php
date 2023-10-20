<?php

namespace App\Http\Controllers\SMSConfigurations\Models;

use App\Http\Controllers\Companies\Models\Company;
use App\Models\BaseModel;

class SmsConfiguration extends BaseModel
{
	protected $table = 'sms_configurations';
	public static $snakeAttributes = false;

	protected $fillable = [
		'company_id',
        'outlet_id',
        'gateway_type',
		'credentials',
        'status',
	];

	public function smsFilters($request){
        
        return SmsConfiguration::when(!empty($request->gateway_type), function ($query) use ($request) {
            return $query->where('gateway_type', '=', $request->gateway_type);
        })
        ->when(!empty($request->status), function ($query) use ($request) {
            return  $query->whereDate('status', '=', $request->status);
        })
        ->when(!empty($request->from), function ($query) use ($request) {
            return  $query->whereDate('created_at', '>=', $request->from);
        })
        ->when(!empty($request->to), function ($query) use ($request) {
            return  $query->whereDate('created_at', '<=', $request->to);
        }); 
    }

    public function refunds()
    {
        return $this->hasMany(FleetRefund::class);
    }

    public function driver() {
        return $this->belongsTo(FleetDriver::class);
    }

}
