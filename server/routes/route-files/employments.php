<?php
use Illuminate\Support\Facades\Route;

    // Employment Module
    Route::post('employment/update', 'EmploymentsController@update');
    Route::post('employment/get_primary_job', 'EmploymentsController@getPrimaryJob');
    Route::post('employment/switch_primary_job', 'EmploymentsController@switchPrimaryJob');
    Route::apiResource('employments', 'EmploymentsController');  
    
?>