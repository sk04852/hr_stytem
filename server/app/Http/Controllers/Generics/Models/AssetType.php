<?php

namespace App\Http\Controllers\Generics\Models;

use App\Http\Controllers\Assets\Models\Asset;
use App\Models\BaseModel;

class AssetType extends BaseModel implements IGeneric
{
    protected $table = 'generic_asset_types';

    protected $fillable = [
        'company_id',
		'name',
		'param_1',
        'param_2'
    ];

    public function asset()
    {
        return $this->hasOne(Asset::class);
    }
}
