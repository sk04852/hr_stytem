<?php

namespace App\Http\Controllers\EmsCompensation\Models;

use App\Http\Controllers\EmsEmployee\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class Compensation extends Model
{
    protected $table = 'compensations';

    protected $fillable = [
        'employee_id',
        'amount',
        'currency',
        'frequency',
        'effective_date',
        'end_date',
        'note',
        'date_difference'
    ];

    public function employee() {
        return $this->belongsTo(Employee::class);
	}
	
}
