<?php
use Illuminate\Support\Facades\Route;

    /* ===== Suppliers ====== */

    Route::apiResource('suppliers', 'SuppliersController');
    Route::post('update_supplier', 'SuppliersController@update');

    /* ===== End ======= */

?>

    