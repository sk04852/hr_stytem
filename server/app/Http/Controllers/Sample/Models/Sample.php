<?php

namespace App\Http\Controllers\Sample\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Models\BaseModel;

class Sample extends BaseModel
{
	protected $table = 'samples';
	public static $snakeAttributes = false;

	protected $fillable = [
		'name'
	];

}
