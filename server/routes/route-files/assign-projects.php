<?php
use Illuminate\Support\Facades\Route;

    /* ===== Assign Projects Start ====== */

    Route::post('assign_projects', 'AssignProjectsController@assignProjectToTeams');
    Route::post('update_assign_projects', 'AssignProjectsController@update');
    Route::delete('delete_assign_projects', 'AssignProjectsController@destroy');
    Route::apiResource('assign_employees', 'AssignProjectsController');
        
    /* ===== Assign Projects End ======= */

?>