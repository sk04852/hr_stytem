<?php

namespace App\Http\Controllers\job_shifts\Models;

use Illuminate\Database\Eloquent\Model;

class job_shifts extends Model
{
    protected $table = 'job_shifts';
    public static $snakeAttributes = false;
    public $timestamps = false;

    protected $fillable = [
        'job_ID',
        'starting_time',
        'ending_time'
    ];

}
