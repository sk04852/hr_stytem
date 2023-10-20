<?php

namespace App\Http\Controllers\Mail\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mews\Purifier\Casts\CleanHtmlInput;

class Template extends Model
{
    use SoftDeletes;

    protected $table = 'mail_templates';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'title',
        'template_key',
        'to',
        'cc',
        'subject',
        'body',
        'created_by'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        // 'title' => CleanHtmlInput::class, // cleans both when getting and setting the value
        // 'template_key' => CleanHtmlInput::class, // cleans both when getting and setting the value
        // 'to' => CleanHtmlInput::class, // cleans both when getting and setting the value
        // 'cc' => CleanHtmlInput::class, // cleans both when getting and setting the value
        // 'subject' => CleanHtmlInput::class, // cleans both when getting and setting the value
        // 'body' => CleanHtmlInput::class, // cleans both when getting and setting the value
    ];

    /**
     * Get the User Pr record associated with the language.
     */
    public function userpr()
    {
        return $this->belongsTo('App\Models\UserPr', 'created_by', 'id');
    }
}
