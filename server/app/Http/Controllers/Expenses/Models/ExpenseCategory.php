<?php

namespace App\Http\Controllers\Expenses\Models;

use App\Models\BaseModel;
use App\Http\Controllers\Users\Models\User;

class ExpenseCategory extends BaseModel
{
	protected $table = 'expense_categories';
	public static $snakeAttributes = false;

	protected $fillable = [
        'name',
        'description',
        'created_by',
        'bg_color',
	];

	public function expenseFilters($request){
        return Expense::when(!empty($request->created_by) , function ($query) use ($request) {
            return $query->where('created_by', '=', $request->created_by);
		})
        ->when(!empty($request->name), function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->name . '%');
		});
	}

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
	}

	public function scopeWithReportedBy($query) {
        return $query->with(['reportedBy' => function($query){ $query->select(['id','first_name','last_name']);}]);
	}

	public function scopeWithCreatedBy($query) {
        return $query->with(['createdBy' => function($query){ $query->select(['id','first_name','last_name']);}]);
	}
}
