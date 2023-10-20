<?php

use Illuminate\Support\Facades\Route;

Route::post('create-brand', 'BrandsController@createBrand');
Route::get('brands-list', 'BrandsController@brandsList');
Route::get('brand-detail/{brand_id}', 'BrandsController@brandDetail');
Route::delete('delete-brand', 'BrandsController@deleteBrand');
Route::post('update_brand', 'BrandsController@updateBrand');
Route::post('brands/mass_delete', 'BrandsController@massDelete');
Route::get('brands/search/{keywords}', 'BrandsController@search');
// Route::post('brands/mass_update', 'BrandsController@massUpdate'); for future 
