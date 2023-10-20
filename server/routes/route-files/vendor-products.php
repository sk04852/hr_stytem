<?php
use Illuminate\Support\Facades\Route;

        /* ===== Products Start =====*/

      Route::group(['prefix'=>'vendor'],function(){
        Route::post('product/update', 'VendorProductsController@update');
        Route::post('products/search/{data}', 'VendorProductsController@search');
        Route::post('products/mass_delete', 'VendorProductsController@performMassDelete');
        Route::post('products/mass_update', 'VendorProductsController@massUpdate');
        Route::get('products/search/{name}', 'VendorProductsController@search');
        Route::post('product/status/{id}', 'VendorProductsController@status');
        Route::post('products/batch_price_update', 'VendorProductsController@updateBatchPrices');
        Route::resource('products', 'VendorProductsController');
        Route::get('products_list', 'VendorProductsController@productsList');
        Route::get('generate_product_code', 'VendorProductsController@generateProductCode');
      });

    /* ===== Products End ===== */

?>
