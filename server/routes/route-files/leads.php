<?php
    use Illuminate\Support\Facades\Route;

    Route::get('leads', 'LeadsController@listAll');
    Route::post('leads', 'LeadsController@create');
    Route::delete('leads/{id}', 'LeadsController@destroy');
    Route::get('leads/{id}', 'LeadsController@getOne');
    Route::get('lead/{id}/comments', 'LeadsController@getCommentsByLeadId');
    Route::post('lead/update', 'LeadsController@update');
    Route::post('lead/comment', 'LeadsController@comment');
    Route::post('lead/comment/update', 'LeadsController@updateComment');
    Route::post('lead/comment/delete', 'LeadsController@deleteComment');
    Route::post('lead/transfer_ownership', 'LeadsController@performTransferOwnership');
    Route::post('lead/mass_delete', 'LeadsController@performMassDelete');
    Route::post('lead/mass_comment', 'LeadsController@performMassComment');

    Route::post('lead/find_duplicates', 'LeadsController@findDuplicates');

    // Route::post('leads/upload_file', 'BulkUploadLeads@uploadFile');
    Route::post('leads/file_headers', 'BulkUploadLeads@fileHeaders');
    Route::post('leads/export', 'BulkUploadLeads@exportLeads');
