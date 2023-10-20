<?php

namespace App\Http\Controllers\ActivityLog\Models;

use App\Models\BaseModel;

class LogActivity extends BaseModel
{
    protected $table = 'log_activities';

    protected $fillable = [

        'type',
        'message',
        'url',
        'method',
        'ip',
        'old_data',
        'new_data',
        'module_id',
        'relation_id',
        'action',
        'created_by_type',
        'created_by',

    ];
}
