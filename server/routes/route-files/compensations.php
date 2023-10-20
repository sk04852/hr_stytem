<?php
use Illuminate\Support\Facades\Route;

// Compensation Module For EMS

    Route::post('compensation/update', 'CompensationsController@update');
    Route::apiResource('compensation', 'CompensationsController');

?>