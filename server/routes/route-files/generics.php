<?php
use Illuminate\Support\Facades\Route;

    Route::get('generic/{type}', 'GenericsController@index');
    Route::post('generic/{type}', 'GenericsController@store');
    Route::get('generic/{type}/{id}', 'GenericsController@show');
    Route::delete('generic/{type}/{id}', 'GenericsController@destroy');
    Route::post('generic/{type}/{id}', 'GenericsController@update');

?>