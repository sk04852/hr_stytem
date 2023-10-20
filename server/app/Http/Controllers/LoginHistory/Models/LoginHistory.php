<?php

namespace App\Http\Controllers\LoginHistory\Models;

use App\Http\Controllers\Users\Models\User;
use App\Models\BaseModel;

class LoginHistory extends BaseModel
{
    protected $table = 'login_history';

    protected $fillable = [

        'user_id',
        'ip',
        'device',
        'status',
        'login_time',
        'logout_time',
        'company_id',
        'type_id'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function scopeWithUser($query)
    {
        return $query->with(['user' => function ($query) {
            $query->select(['id', 'first_name', 'last_name', 'email']);
        }]);
    }

    public function LoginHistoryFilters($request)
    {
        return $this->when(!empty($request->status), function ($query) use ($request) {
            return $query->where('status', 'like', '%' . $request->status . '%');
        })->when(!empty($request->user_id), function ($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
            ->when(!empty($request->ip), function ($query) use ($request) {
                return $query->where('ip', $request->ip);
            })
            ->when(!empty($request->device), function ($query) use ($request) {
                return $query->where('device','LIKE','%' .$request->device. '%');
            })
            ->when(!empty($request->created_at), function ($query) use ($request) {
                return $query->where('created_at','LIKE','%' .$request->created_at. '%');
            })
            ->when(!empty($request->updated_at), function ($query) use ($request) {
                return $query->where('updated_at','LIKE','%' .$request->updated_at. '%');
            })
            ->when(!empty($request->status), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when(!empty($request->from), function ($query) use ($request) {
				return  $query->where('login_time', '>=', $request->from);
			})
			->when(!empty($request->to), function ($query) use ($request) {
				return  $query->where('login_time', '<=', $request->to);
			})
            ->when(!empty($request->type_id), function ($query) use ($request) {
                return $query->where('type_id', $request->type_id);
            });
    }


}
