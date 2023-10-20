<?php

namespace App\Http\Controllers\Timezones\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timezone extends Model
{
    use SoftDeletes;

    protected $table = 'timezones';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'timezone',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the Candidate CV record associated with the language.
     */
    public function userpr()
    {
        return $this->hasMany('App\Models\UserPr', 'timezone_id', 'id');
    }
}
