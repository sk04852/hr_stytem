<?php

namespace App\Http\Controllers\Notifications\Models;

use App\Http\Controllers\Users\Models\User;
use App\Models\BaseModel;

/**
 * @property integer $notification_id
 * @property integer $receiver_id
 * @property string $read_at
 * @property string $created_at
 * @property string $updated_at
 * @property Notification $notification
 * @property User $user
 */
class NotificationReceiver extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = ['notification_id', 'receiver_id', 'read_at', 'created_at', 'updated_at'];


    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
