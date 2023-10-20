<?php
use Illuminate\Support\Facades\Route;

Route::get('system/update_permission', 'RBACLController@getSystemPermissions');

Route::group(['namespace'=>'Permissions','prefix'=>'rbacl'],function(){
    Route::apiResource('permissions', 'PermissionsController');
    Route::post('update_permission', 'PermissionsController@update');
});


Route::group(['namespace'=>'Profiles','prefix'=>'rbacl'],function(){
    Route::apiResource('profiles', 'RBACLProfilesController');
    Route::post('update_profile', 'RBACLProfilesController@update');
});

Route::group(['namespace'=>'Roles','prefix'=>'rbacl'],function(){
    Route::apiResource('roles', 'RBACLRolesController');
    Route::post('update_role', 'RBACLRolesController@update');
    Route::post('test', 'RBACLRolesController@test');
});


Route::group(['namespace'=>'AssignPermissions','prefix'=>'rbacl'],function(){
    Route::post('assign_permissions', 'AssignPermissionController@store');
    Route::post('check_permission', 'AssignPermissionController@checkPermission');
});

Route::group(['namespace'=>'RevokePermissions','prefix'=>'rbacl'],function(){
    Route::post('revoke_permissions', 'RevokePermissionController@revoke');
});
?>
