<?php
use Illuminate\Support\Facades\Route;

    // SMS Configurations
    Route::post('update-sms-configuration', 'SmsConfigurationsController@update');
    Route::apiResource('sms-configurations', 'SmsConfigurationsController');
    
?>