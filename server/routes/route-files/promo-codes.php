<?php

use Illuminate\Support\Facades\Route;

Route::post('create-promo-code', 'PromoCodesController@createPromoCode');
Route::get('promo-codes-list', 'PromoCodesController@promoCodesList');
Route::get('promo-code-detail/{promo_code_id}', 'PromoCodesController@promoCodeDetail');
Route::delete('delete-promo_code', 'PromoCodesController@deletePromoCode');
Route::post('update_promo_code', 'PromoCodesController@updatePromoCode');
Route::get('search-promo-code/{promocode}', 'PromoCodesController@searchPromoCode');
