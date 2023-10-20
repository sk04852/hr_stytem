<?php

namespace App\Http\Controllers\Notifications\Models;

use App\Http\Controllers\Users\Models\User;
use App\Models\BaseModel;

class Notification extends BaseModel
{
    protected $fillable = [
        'type',
        'payload',
        'body',
        'delivered',
    ];

    public function notificationFilters($request) {
        return Notification::when(!empty($request->type), function ($query) use ($request) {
            return $query->where('type', '=', $request->type);
        })
        ->when(!empty($request->receiver_id), function ($query) use ($request) {
            $query->where('receiver_id', '=', $request->receiver_id);
		})
        ->when(!empty($request->read), function ($query) use ($request) {
            $query->where('read', '=', $request->read);
		})
        ->when(!empty($request->delivered), function ($query) use ($request) {
            $query->where('delivered', '=', $request->delivered);
		})
        ->when(!empty($request->from), function ($query) use ($request) {
            return  $query->whereDate('created_at', '>=', $request->from);
        })
        ->when(!empty($request->to), function ($query) use ($request) {
            return  $query->whereDate('created_at', '<=', $request->to);
        });
    }

    public function users()
    {
        $this->morphedByMany(User::class, 'receiver');
    }

    public function receivers()
    {
        return $this->belongsToMany(User::class, 'notification_receivers', 'notification_id', 'receiver_id')->select(['id', 'first_name', 'last_name', 'email']);
    }

    public function clients()
    {
        $this->morphedByMany(Client::class, 'receiver');
    }

    public function unreadUserNotifications()
    {
        return NotificationReceiver::where('receiver_type', '=', 'User')->where('read_at', null);
    }

    public function unreadClientNotifications()
    {
        return NotificationReceiver::where('receiver_type', '=', 'Client')->where('read_at', null);
    }

    public function userNotifications()
    {
        return NotificationReceiver::where('receiver_type', '=', 'User');
    }

    public function clientNotifications()
    {
        return NotificationReceiver::where('receiver_type', '=', 'Client');
    }
}
