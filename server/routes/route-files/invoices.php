<?php
use Illuminate\Support\Facades\Route;

    /* ===== Invoices ====== */
    Route::group(['prefix' => 'invoices'], function(){

        Route::get('calculate_item', 'InvoicesController@calculateProductPriceForInvoice');
        Route::get('createInvoiceNumber', 'InvoicesController@createRandomNumber');
        Route::post('update', 'InvoicesController@update');
        Route::get('send_invoice/{invoiceId}', 'InvoicesController@sendInvoiceViaEmail');
        Route::get('generate_pdf/{invoiceId}','InvoicesController@generatePDF');
        Route::get('files/{invoiceId}','InvoicesController@getFiles');
        Route::get('generate_invoice_code', 'InvoicesController@generateInvoiceCode');
        Route::get('download/{invoiceId}', 'InvoicesController@downloadInvoicePDF');

    });
    Route::apiResource('invoices', 'InvoicesController');

    /* ===== End ======= */

?>

