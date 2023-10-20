<?php
    use Illuminate\Support\Facades\Route;
    Route::apiResource('assets', 'AssetsController');
    Route::post('assets/update', 'AssetsController@update');
?>