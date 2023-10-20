<?php

use Illuminate\Support\Facades\Route;

// Transaction Type Module
Route::post('transaction_types/mass_delete', 'TransactionTypesController@MassDelete');
Route::post('transaction_type/update', 'TransactionTypesController@update');
Route::apiResource('transaction_types', 'TransactionTypesController');
