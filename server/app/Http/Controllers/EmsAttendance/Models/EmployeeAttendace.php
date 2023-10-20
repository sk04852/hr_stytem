<?php

namespace App\Http\Controllers\EmsAttendance\Models;


use App\Models\BaseModel;
use App\Http\Controllers\EmsEmployee\Models\Employee;

class EmployeeAttendace extends BaseModel
{
    protected $table = 'employee_attendance';
    public static $snakeAttributes = false;

    protected $fillable = [
        'employee_id',
        'project_id',
        'date',
        'start_time',
        'entry_type',
        'end_time',
        'source',
        'note'
    ];

    protected $hidden = ['updated_at', 'created_at'];

    public function employee()
    {
        return $this->belongsToMany(Employee::class);
    }
}
