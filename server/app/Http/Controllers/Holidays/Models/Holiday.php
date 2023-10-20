<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Controllers\Holidays\Models;

use App\Models\BaseModel;

class Holiday extends BaseModel
{
    protected $table = 'holidays';

    public static $snakeAttributes = false;

    protected $fillable = [
        'title',
        'date',
        'country_code',
    ];

}
