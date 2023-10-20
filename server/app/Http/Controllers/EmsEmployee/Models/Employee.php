<?php

namespace App\Http\Controllers\EmsEmployee\Models;

use App\Http\Controllers\AssignEmployees\Models\AssignEmployee;
use App\Http\Controllers\Companies\Models\Company;
use App\Http\Controllers\Generics\Models\Designation;
use App\Http\Controllers\People\Interfaces\IPerson;
use App\Http\Controllers\People\Models\Person;
use App\Http\Controllers\EmsAttendance\Models\EmployeeAttendace;
use App\Http\Controllers\EmsCompensation\Models\Compensation;
use App\Http\Controllers\FleetsDrivers\Models\FleetDriver;
use App\Http\Controllers\Users\Models\User;

class Employee extends Person implements IPerson
{
    protected $table = 'employees';
    public static $snakeAttributes = false;

    protected $fillable = [
        'user_id',
        'employee_number',
        'address',
        'city',
        'country',
        'state',
        'zip',
        'postal_code',
        'image',
        'phone',
        'mobile',
        'salary',
        'facebook_links',
        'linkedin_links',
        'twitter_links',
        'marital_status',
        'profile_picture',
        'nationality',
        'personal_phone',
        'personal_email',
        'gender',
        'ec_full_name',
        'ec_phone',
        'ec_email',
        'about_me',
        'birth_date',
        'ec_relationship'
    ];

    public function designations()
    {
        return $this->belongsToMany(Designation::class);
    }
    

    public function attendance()
    {
        return $this->hasMany(EmployeeAttendace::class);
    }
    public function ForThisCompany(int $companyId) {
        return User::where('company_id', $companyId);
    }
    

    public function toDigest() {
        return $this->select(['id', 'first_name', 'last_name', 'email'])->get();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function drivers()
    {
        return $this->hasMany(FleetDriver::class);
    }

    public function compensations()
    {
        return $this->hasMany(Compensation::class);
    }

    public function employeeFilters($request, $query)
    {
        return $query->
        when(!empty($request->email), function ($query) use ($request) {
            return $query->where('email','like', '%' . $request->email. '%');
        })
        ->when(!empty($request->first_name), function ($query) use ($request) {
            return $query->where('first_name','like', '%' . $request->first_name. '%');
        })
        ->when(!empty($request->last_name), function ($query) use ($request) {
            return $query->where('last_name','like', '%' . $request->last_name. '%');
        })
        ->when(!empty($request->employee_number), function ($query) use ($request) {
            return $query->where('employee_number', $request->employee_number);
        })
        ->when(!empty($request->birth_date), function ($query) use ($request) {
            return $query->where('birth_date', $request->birth_date);
        });
    }

}
