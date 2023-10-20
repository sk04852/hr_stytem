<?php

namespace App\Http\Controllers\Video\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    protected $table = 'videos';
    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'file_name',
        'path',
        'video_link'
    ];

    /**
     * Get the CandidateCV record associated with the Job History.
     */

    public function getPathAttribute($value)
    {
        return asset('storage/'.$value);
    }

}
