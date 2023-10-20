<?php

namespace App\Http\Controllers\TimeOff\Models;


use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\EmsEmployee\Models\Employee;

class EmployeeTimeOff extends Model
{
    protected $table = 'time_offs';
    public static $snakeAttributes = false;
    protected $fillable = [
        'employee_id',
        'timeoff_type_id',
        'from_date',
        'time_off_for_from',
        'to_date',
        'time_off_for_to',
        'absent_days',
        'note',
        'status',
        'admin_time_off_note',
        'attachment',
        'company_id'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function type()
    {
        return $this->belongsTo(TimeoffType::class, 'timeoff_type_id');
    }
}
