<?php

namespace App\Http\Controllers\Expenses\Models;

use App\Http\Controllers\FleetsMaintenance\Models\FleetMaintenance;
use App\Models\BaseModel;
use App\Http\Controllers\Generics\Models\Category;
use App\Http\Controllers\Users\Models\User;

class Expense extends BaseModel
{
	protected $table = 'expenses';
	public static $snakeAttributes = false;

	protected $fillable = [
		'merchant',
		'date',
		'amount',
		'currency',
		'category_id',
		'image',
		'created_by_id',
		'reporter_id',
		'description',
        'company_id',
        'status',
        'reimbursable',
        'description'
	];

	public function expenseFilters($request){

        return Expense::when(!empty($request->category_id) , function ($query) use ($request) {
            return $query->where('category_id', '=', $request->category_id);
		})
        ->when(!empty($request->currency), function ($query) use ($request) {
            return $query->where('currency', 'like', '%' . $request->currency . '%');
		})
		->when(!empty($request->date_from), function ($query) use ($request) {
            return  $query->where('date', '>=', $request->date_from);
        })
        ->when(!empty($request->date_to), function ($query) use ($request) {
            return  $query->where('date', '<=', $request->date_to);
		})
		->when(!empty($request->first_name), function ($query) use ($request) {
            $query->whereHas('reportedBy', function ($query) use ($request) {
				$query->where('first_name', 'like', '%' .$request->first_name. '%');
            });
		})
		->when(!empty($request->last_name) , function ($query) use ($request) {
            $query->whereHas('reportedBy', function ($query) use ($request) {
				$query->where('last_name', 'like', '%' . $request->last_name . '%');
            });
		});
	}

    public function expenseCategory() {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
	}

	public function scopeWithExpenseCategory($query) {
        return $query->with(['expenseCategory' => function($query){ $query->select(['id','name','bg_color','description']);}]);
    }

    public function reportedBy() {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by_id');
	}

	public function scopeWithReportedBy($query) {
        return $query->with(['reportedBy' => function($query){ $query->select(['id','first_name','last_name']);}]);
	}

	public function scopeWithCreatedBy($query) {
        return $query->with(['createdBy' => function($query){ $query->select(['id','first_name','last_name']);}]);
	}

	public function maintenances()
    {
        return $this->hasMany(FleetMaintenance::class);
    }
}
