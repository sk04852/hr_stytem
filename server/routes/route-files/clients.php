<?php
use Illuminate\Support\Facades\Route;
/* ===== Clients Start =====*/
    Route::post('verify-account', 'ClientsController@verify');
    Route::post('forgot-password', 'ClientsController@forgotPassword');
    Route::post('reset-password', 'ClientsController@resetPassword');
    Route::post('client/update','ClientsController@update');
    Route::post('client/resend-token','ClientsController@resendToken');
    Route::post('change-password', 'ClientsController@changePassword');
    Route::post('create-client', 'ClientsController@store');
    Route::get('clients_list', 'ClientsController@clientsList');
    Route::apiResource('clients', 'ClientsController');



/* ===== Clients End =====*/


Route::group(['prefix' => 'client'], function() {

    Route::apiResource('addresses', 'ClientsAddressesController');
    Route::post('update-address', 'ClientsAddressesController@update');
    Route::post('product_prices', 'ClientsController@addProductPrices');
    Route::get('{clientId}/product_prices', 'ClientsController@getProductPrices');

    // Routes for client frontend
    Route::get('summery', 'ClientsController@summery');
    Route::get('payments', 'ClientsController@payments');
    Route::get('statement', 'ClientsController@statements');
    Route::get('invoices', 'ClientsController@invoices');
    Route::get('estimates', 'ClientsController@estimates');
    Route::post('logout', 'ClientsController@clientLogout');
    Route::post('profile-update','ClientsController@updateProfile');
    Route::get('send_client_statement', 'ClientsController@sendClientStatementViaEmail');
    Route::post('update-client-address','ClientsController@updateAddress');
});

?>
