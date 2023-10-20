<?php

namespace App\Http\Controllers\PeopleLookup\Models;
use App\Models\BaseModel;


class PeopleLookup extends BaseModel
{
	protected $table = 'v_people';
	public static $snakeAttributes = false;

	protected $fillable = [

    ];

    public function scopeLookup($query, string $keyword) {
        return $query->where('first_name', 'like', '%'.$keyword.'%')->orWhere('last_name', 'like', '%'.$keyword,'%');
    }

    public function scopeType($query, string $type) {
        return $query->where('Type', $type);
    }

}
