<?php
use Illuminate\Support\Facades\Route;

    /* ===== Pre Orders ====== */
    Route::group(['prefix' => 'estimates'], function(){
        
        Route::get('calculate_item', 'EstimatesController@calculateProductPriceForPreOrder');
        Route::post('update', 'EstimatesController@update');
    });
    Route::apiResource('estimates', 'EstimatesController');
    Route::get('estimate_conversion/{id}', 'EstimatesController@estimateConversionToInvoice');

    /* ===== End ======= */

?>

    