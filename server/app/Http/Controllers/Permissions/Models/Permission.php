<?php

namespace App\Http\Controllers\Permissions\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    public static $snakeAttributes = false;
    public $timestamps = false;

    protected $fillable = [
        'Role-ID',
        'NAME',
        'Permission_id'
    ];

    public function PermissionsFilter($request){

        return Permission::when(!empty($request->RoleID), function ($query) use ($request) {
            return $query->where('Role-ID', '=', $request->RoleID);
        })
        ->when(!empty($request->name), function ($query) use ($request) {
            $query->where('NAME', 'like', '%' . $request->name . '%');
        });
    }

}
