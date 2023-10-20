<?php

namespace App\Http\Controllers\Companies\Models;

use Illuminate\Database\Eloquent\Model;

class Rik extends Model
{
    protected $table = 'rik_response';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'response',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}
