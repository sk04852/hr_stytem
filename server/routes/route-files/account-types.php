<?php
    use Illuminate\Support\Facades\Route;
    Route::apiResource('account_types', 'AccountTypesController');
    Route::post('update_account_type', 'AccountTypesController@update');
?>