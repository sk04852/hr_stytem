<?php
use Illuminate\Support\Facades\Route;

  // Companies
  Route::post('update_company', 'CompaniesController@update');
  Route::apiResource('companies', 'CompaniesController');
