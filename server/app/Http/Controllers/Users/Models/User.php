<?php

namespace App\Http\Controllers\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $table = 'users';
    public static $snakeAttributes = false;
    public $timestamps = true;

    protected $fillable = [
        'user_ID',
        'name',
        'job_title',
        'location',
    ];

    public function UsersFilter($request){

        return User::when(!empty($request->UserID), function ($query) use ($request) {
            return $query->where('User-ID', '=', $request->UserID);
        })
        ->when(!empty($request->name), function ($query) use ($request) {
            $query->where('Name', 'like', '%' . $request->name . '%');
        });
    }

    public function user_pr (){
        return $this->belongsTo('App\Models\UserPr', 'user_ID', 'id');
    }

}
