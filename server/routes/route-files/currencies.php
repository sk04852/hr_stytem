<?php
use Illuminate\Support\Facades\Route;

  // Currencies
  Route::post('currencies/update', 'CurrenciesController@update');
  Route::apiResource('currencies', 'CurrenciesController');
?>