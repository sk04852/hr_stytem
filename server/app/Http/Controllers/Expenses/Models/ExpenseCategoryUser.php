<?php

namespace App\Http\Controllers\Expenses\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class ExpenseCategoryUser extends BaseModel
{
    protected $table = 'expense_category_users';
	protected $fillable = [
        'user_id',
        'category_id'
    ];
}
