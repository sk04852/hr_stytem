<?php

namespace App\Http\Controllers\job_work_lang\Models;

use Illuminate\Database\Eloquent\Model;

class job_work_lang extends Model
{
    protected $table = 'job_work_lang';
    public static $snakeAttributes = false;
    public $timestamps = false;

    protected $fillable = [
        'job_ID',
        'languages',
    ];

}
