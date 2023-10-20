<?php

namespace App\Http\Controllers\Mail\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mews\Purifier\Casts\CleanHtmlInput;

class Wildcard extends Model
{
    use SoftDeletes;

    protected $table = 'mail_wildcards';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'wildcard_key',
        'wildcard_value',
        'created_by'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        // 'wildcard_key' => CleanHtmlInput::class, // cleans both when getting and setting the value
        // 'wildcard_value' => CleanHtmlInput::class, // cleans both when getting and setting the value
    ];

    /**
     * Get the User Pr record associated with the language.
     */
    public function userpr()
    {
        return $this->belongsTo('App\Models\UserPr', 'created_by', 'id');
    }
}
