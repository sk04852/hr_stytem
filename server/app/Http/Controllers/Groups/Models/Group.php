<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Controllers\Groups\Models;

use App\Http\Controllers\Users\Models\User;
use App\Models\BaseModel;

class Group extends BaseModel
{
    protected $table = 'groups';

    public static $snakeAttributes = false;

    protected $fillable = [
        'company_id',
        'group_name',
        'is_default',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
