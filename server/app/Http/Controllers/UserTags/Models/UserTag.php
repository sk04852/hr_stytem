<?php

namespace App\Http\Controllers\UserTags\Models;

use Illuminate\Database\Eloquent\Model;

class UserTag extends Model
{
    protected $table = 'user-tags';
    public static $snakeAttributes = false;
    public $timestamps = false;

    protected $fillable = [
        'Tag-ID',
        'Name',
    ];

    public function UserTagsFilter($request){

        return UserTag::when(!empty($request->TagID), function ($query) use ($request) {
            return $query->where('Tag-ID', '=', $request->TagID);
        })
        ->when(!empty($request->name), function ($query) use ($request) {
            $query->where('Name', 'like', '%' . $request->name . '%');
        });
    }

}
