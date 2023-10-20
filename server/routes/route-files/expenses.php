<?php
use Illuminate\Support\Facades\Route;
    // Expense Categories
    Route::post('expense_category', 'ExpensesController@storeCategory');
    Route::put('expense_category/update', 'ExpensesController@updateCategory');
    Route::get('expense_categories', 'ExpensesController@listCateory');
    Route::delete('expense_category/{id}', 'ExpensesController@destroyExpense');

    //Expenses
    Route::get('expenses', 'ExpensesController@index');
    Route::post('add_expense', 'ExpensesController@store');
    Route::get('expense/{id}', 'ExpensesController@show');
    Route::post('expense', 'ExpensesController@update');
    Route::delete('expense/{id}', 'ExpensesController@destroy');
    Route::get('current_expenses', 'ExpensesController@expenses');
    Route::get('expense_chart', 'ExpensesController@chart');



?>
