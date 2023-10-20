<?php

namespace App\Http\Controllers\Assets\Models;

use App\Http\Controllers\Generics\Models\AssetType;
use App\Http\Controllers\Generics\Models\Location;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Models\BaseModel;

class Asset extends BaseModel
{
	protected $table = 'assets';
	public static $snakeAttributes = false;

	protected $fillable = [

        'company_id',
        'asset_name',
		'asset_type_id',
		'location_id',
        'serial_number',
        'assign_to',
        'colour',
        'description'
	];

    public function assetFilters($request)
    {
        return $this->when(!empty($request->asset_name), function ($query) use ($request) {
            return $query->where('asset_name', 'like', '%' . $request->asset_name . '%');
        })
        ->when(!empty($request->serial_number), function ($query) use ($request) {
            return $query->where('serial_number', 'like', '%' . $request->serial_number . '%');
        });
    }

    public function assetType() {
        return $this->belongsTo(AssetType::class, 'asset_type_id');
    }

	public function scopeWithAsset($query) {
        return $query->with(['assetType' => function($query){ $query->select(['id','name']);}]);
    }
    
    public function location() {
        return $this->belongsTo(Location::class, 'location_id');
    }

	public function scopeWithLocation($query) {
        return $query->with(['location' => function($query){ $query->select(['id','name']);}]);
	}
}
