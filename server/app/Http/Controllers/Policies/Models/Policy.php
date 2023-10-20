<?php

namespace App\Http\Controllers\Policies\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Models\BaseModel;

class Policy extends BaseModel
{
	protected $table = 'policies';
	public static $snakeAttributes = false;

	protected $fillable = [
        'name',
        'company_id'
	];

}
