<?php
use Illuminate\Support\Facades\Route;

    /* ===== Tools ====== */

    Route::apiResource('tools', 'ToolsController');
    Route::post('update_tools', 'ToolsController@update');

    /* ===== End ======= */

?>

    