<?php
use Illuminate\Support\Facades\Route;

    // Payments
    Route::post('update-payment', 'PaymentsController@update');
    Route::apiResource('payment', 'PaymentsController');
?>
