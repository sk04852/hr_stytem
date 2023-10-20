<?php

namespace App\Http\Controllers\job_desired_lang\Models;

use Illuminate\Database\Eloquent\Model;

class job_desired_lang extends Model
{
    protected $table = 'job_desired_lang';
    public static $snakeAttributes = false;
    public $timestamps = false;

    protected $fillable = [
        'job_ID',
        'language',
    ];

}
