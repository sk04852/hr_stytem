<?php

use Illuminate\Support\Facades\Route;

/* ===== Reports ====== */

Route::group(['prefix' => 'reports'], function () {
    Route::get('transactions_report', 'ReportsController@tradingPlateFormAccount');
});
/* ===== End ======= */
