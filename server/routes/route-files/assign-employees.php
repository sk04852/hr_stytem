<?php
use Illuminate\Support\Facades\Route;

    /* ===== Assign Employees Start ====== */

  
    Route::post('assign_employees_to_team', 'AssignEmployeesController@assignEmployeeToTeam');
    Route::post('update_assign_employees', 'AssignEmployeesController@update');
    Route::delete('delete_assign_employees', 'AssignEmployeesController@destroy');
    Route::apiResource('assign_employees', 'AssignEmployeesController');
        
    /* ===== Assign Employees End ======= */

?>