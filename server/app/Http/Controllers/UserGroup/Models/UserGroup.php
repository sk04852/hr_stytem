<?php

namespace App\Http\Controllers\UserGroup\Models;

use App\Models\BaseModel;

class UserGroup extends BaseModel
{
    protected $table = 'user_groups';
    public static $snakeAttributes = false;

    protected $fillable = [
        'group_id',
        'user_id',
    ];
}
