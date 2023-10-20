<?php

namespace App\Http\Controllers\Medias\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaDirectory extends BaseModel
{
    use SoftDeletes;
	protected $table = 'media_directories';
	public static $snakeAttributes = false;

	protected $fillable = [
        "name",
        "location",
        "created_by"
    ];
}
