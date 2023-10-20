<?php
use Illuminate\Support\Facades\Route;

    /* ===== Groups Start ====== */

        Route::apiResource('groups', 'GroupsController');
        Route::post('update_group', 'GroupsController@update');

    /* ===== Groups End ======= */

?>
