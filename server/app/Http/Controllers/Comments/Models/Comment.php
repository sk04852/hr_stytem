<?php

namespace App\Http\Controllers\Comments\Models;
use App\Models\BaseModel;
use App\Http\Controllers\Users\Models\User;

class Comment extends BaseModel
{
	protected $table = 'comments';
	public static $snakeAttributes = false;

	protected $fillable = [
		'module_id',
		'relation_id',
		'created_by',
		'last_commented_by',
		'comment',
		'is_reply',
		'reply_to'
    ];

    protected static function booted()
    {
        static::saved(function($model){
        });
    }

    public function commenter()
	{
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function scopeWithCommenter($query) {
        return $query->with(['commenter' => function($query){ $query->select(['id', 'first_name', 'last_name']);}]);
    }
}
