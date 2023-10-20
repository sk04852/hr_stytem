<?php
use Illuminate\Support\Facades\Route;

    /* ===== User Groups Start ====== */

        Route::post('update_group_user', 'UserGroupController@update');
        Route::post('delete_group_user', 'UserGroupController@destroy');
        Route::apiResource('group_user', 'UserGroupController');
        
    /* ===== User Groups End ======= */

?>