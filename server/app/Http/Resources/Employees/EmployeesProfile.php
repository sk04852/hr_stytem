<?php

namespace App\Http\Resources\Employees;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\Employments\Models\Employment;
use App\Http\Services\EmploymentService;

class EmployeesProfile extends JsonResource
{

    public function toArray($request)
    {
        return [
            
            'account_detail' => [

                'user_id'=>$this->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'status' => $this->status,

            ],
            'profile_detail' => [  
                'employee_id'=> $this->employee ? $this->employee->id : $this->id,
                'employee_number'=> $this->employee ? $this->employee->employee_number : $this->employee_number,
                'gender'=> $this->employee ? $this->employee->gender : $this->gender,
                'phone'=> $this->employee ? $this->employee->phone : $this->phone,
                'mobile'=> $this->employee ? $this->employee->mobile : $this->mobile,
                'address'=> $this->employee ? $this->employee->address : $this->address,
                'city'=> $this->employee ? $this->employee->city : $this->city,
                'country'=> $this->employee ? $this->employee->country : $this->country,
                'code'=> $this->employee ? $this->employee->code : $this->code,
                'state'=> $this->employee ? $this->employee->state : $this->state,
                'zip'=> $this->employee ? $this->employee->zip : $this->zip,
                'image'=> $this->employee ? $this->employee->image : $this->image,
                'image'=> $this->employee ? $this->employee->image : $this->image,
                'image'=> $this->employee ? $this->employee->image : $this->image,
                'about_me'=> $this->employee ? $this->employee->about_me : $this->about_me,
                'facebook_links'=> $this->employee ? $this->employee->facebook_links : $this->facebook_links,
                'linkedin_links'=> $this->employee ? $this->employee->linkedin_links : $this->linkedin_links,
                'birth_date'=> $this->employee ? $this->employee->birth_date : $this->birth_date,
                'marital_status'=> $this->employee ? $this->employee->marital_status : $this->marital_status,
                'nationality'=> $this->employee ? $this->employee->nationality : $this->nationality,
                'personal_phone'=> $this->employee ? $this->employee->personal_phone : $this->personal_phone,
                'personal_email'=> $this->employee ? $this->employee->personal_email : $this->personal_email,
                'ec_full_name'=> $this->employee ? $this->employee->ec_full_name : $this->ec_full_name,
                'ec_email'=> $this->employee ? $this->employee->ec_email : $this->ec_email,
                'ec_relationship'=> $this->employee ? $this->employee->ec_relationship : $this->ec_relationship,
                'created_at'=> $this->employee ? $this->employee->created_at : $this->created_at,

            ],

            'employments_detail' => Employment::from('employees as e')
            ->join('employments as em','e.id','=','em.employee_id')
            ->leftJoin('generic_work_schedules as gws','em.work_schedule_id','gws.id')
            ->leftJoin('generic_designations as gd','em.position_id','gd.id')
            ->leftJoin('generic_teams as gt','em.team_id','gt.id')
            ->leftJoin('generic_locations as gl','em.location_id','gl.id')
            ->select(['e.id','gws.id as work_schedule_id','gws.name as work_schedule',
                      'gd.id as designation_id','gd.name as designation','gt.id as team_id',
                      'gt.name as team','gl.id as location_id',
                      'gl.name as location','em.created_at'])
            ->where('em.employee_id',$this->id)->get(),
        ];
    }
}
