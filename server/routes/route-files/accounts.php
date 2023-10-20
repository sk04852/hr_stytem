<?php
    use Illuminate\Support\Facades\Route;
    Route::group(['prefix' => 'accounts'], function ($router) {
        Route::get('/list', 'AccountsController@index');
        Route::post('/register', 'AccountsController@createAcccount');
        Route::post('update', 'AccountsController@accountUpdate');
        Route::get('single_account/{id}', 'AccountsController@profile');
        Route::get('extended_profile/{id}', 'Users\UsersController@getExtendedProfile');
        Route::post('account/comment', 'AccountsController@comment');
        Route::post('account/comment/update', 'AccountsController@updateComment');
        Route::post('account/comment/delete', 'AccountsController@deleteComment');
        Route::post('/transfer_ownership', 'AccountsController@performTransferOwnership');
        Route::delete('delete/{id}', 'AccountsController@destroy');
        Route::post('change_password/{id}', 'AccountsController@changeAccountPassword');
        Route::post('/mass_delete', 'AccountsController@accountMassDelete');
        Route::post('import', 'BulkUploadAccounts@importAccounts');
        Route::post('export', 'BulkUploadAccounts@exportAccounts');
});
