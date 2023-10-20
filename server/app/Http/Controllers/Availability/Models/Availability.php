<?php

namespace App\Http\Controllers\Availability\Models;

use App\Models\BaseModel;

class Availability extends BaseModel
{
    protected $table = 'availabilities';
    public static $snakeAttributes = false;

	protected $fillable = [
        'name',
	];

}
