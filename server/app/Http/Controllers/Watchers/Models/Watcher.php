<?php

namespace App\Http\Controllers\Watchers\Models;

use App\Http\Controllers\Users\Models\User;
use App\Models\BaseModel;

class Watcher extends BaseModel
{
    protected $table = 'watchers';
    
	protected $fillable = [
        'module_id',
        'relation_id',
        'user_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
