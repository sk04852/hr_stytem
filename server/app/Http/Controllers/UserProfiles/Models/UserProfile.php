<?php

namespace App\Http\Controllers\UserProfiles\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_pr';
    public static $snakeAttributes = false;
    public $timestamps = false;

    protected $fillable = [
       'userpr_ID',
       'user_ID',
       'photo',
       'int_code',
       'phone',
       'email',
       'password',
       'timezone',
       'permission_ID',
    ];

    public function UserProfilesFilter($request){

        return UserProfile::when(!empty($request->UserID), function ($query) use ($request) {
            return $query->where('User-ID', '=', $request->UserID);
        })
        ->when(!empty($request->UserprID), function ($query) use ($request) {
            $query->where('Userpr-ID', $request->UserprID);
        });
    }

}
