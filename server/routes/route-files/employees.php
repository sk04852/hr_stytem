<?php
use Illuminate\Support\Facades\Route;

  // Employees
  Route::get('employees', 'EmployeeController@employeeList');
  Route::get('employee_profile', 'EmployeeController@employeeProfile');
  Route::post('employees', 'EmployeeController@employeeRegister');
  Route::get('employees/list', 'EmployeeController@employeeList');
  Route::get('employees/digest', 'EmployeeController@employeeDigest');
  Route::post('employees/login', 'EmployeeController@login');
  Route::post('employees/generateCodeUsingEmail', 'EmployeeController@generateCodeUsingEmail');
  Route::delete('employees/delete/{id}', 'EmployeeController@destroy');
  Route::post('employees/update', 'EmployeeController@updateEmployee');
  Route::post('employees/updatePassword', 'EmployeeController@updatePassword');
  Route::post('employees/reset_employee_password', 'EmployeeController@resetMyCompanyEmployeePassword');
  Route::post('employees/forgot_password', 'EmployeeController@forgotEmployeePassword');
  Route::post('employees/reset_password', 'EmployeeController@resetEmployeePassword');
  Route::post('employees/verify_employee_email_by_token', 'EmployeeController@verifyEmployeeEmailByToken');
  Route::post('employees/send_verification_token', 'EmployeeController@resendEmailVerificationToken');
  Route::post('employees/assign_designation', 'EmployeeController@assign_designation');
  Route::post('employees/assign_designation', 'EmployeeController@assign_designation');
  Route::post('employees/change_employee_password_by_admin','EmployeeController@changeEmployeePasswordByAdmin');
  

?>