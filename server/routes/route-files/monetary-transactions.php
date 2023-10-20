<?php

use Illuminate\Support\Facades\Route;

/* ===== Monetary Transactions ====== */

Route::post('monetary_transactions/mass_delete', 'MonetaryTransactionsController@massDelete');
Route::apiResource('monetary_transactions', 'MonetaryTransactionsController');
