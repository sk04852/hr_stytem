<?php
namespace App\Http\Services;

use App\Http\Controllers\EmsEmployee\EmployeeController;
use App\Http\Controllers\EmsEmployee\Models\Employee;
use App\Http\Controllers\Generics\Models\WorkSchedule;
use App\Http\Controllers\People\PeopleController;
use App\Http\Controllers\Users\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class EmployeesService extends PeopleController{ 
    
    private $model_;
    private $userService_;
    private $defaultWorkScheduleId_ = 4;
    
    public function __construct(Employee $employeeModel, 
                                UserService $userService) {
        $this->model_ = $employeeModel;
        $this->userService_ = $userService;
    }

    public function getEmployees(array $condition) {
        return $this->model_w->where($condition)->with('user')->paginate(10);
    }

    public function getEmployeesByCompanyId(int $companyId) {
        $userIds = $this->userService_->getUserIdsByCompanyId($companyId);
        return $this->model_->whereIn('user_id', $userIds)->with('user')->get();
    }

    public function getEmployeeWorkSchedule(Employee $employee) {
        if($employee->work_schedule_id === null) {
            $workSchedule = WorkSchedule::where('id', $this->defaultWorkScheduleId_)->first();
            return $workSchedule;
        }
        return $employee->workSchedule;
    }

    public function getEmployee(int $employeeId) {
        return $this->model_->where('id', $employeeId)->with('user')->first();
    }

    public function getEmployeesByIds(array $employeeIds) {
        return $this->model_->whereIn('id', $employeeIds)->with('user')->get();
    }

    public function employeesToDigest($employees) {
        $digest = [];
        foreach($employees as $employee) {
            $digest[] = [
                'id'=> $employee->id,
                'user_id'=> $employee->user->id,
                'first_name'=> $employee->user->first_name,
                'last_name'=> $employee->user->last_name,
                'email'=> $employee->user->email,
            ];
        }

        return $digest;
    }

    public function getCompanyId($request)
    {
         $companyId = $request->get('company_id');
        if ( $companyId == null ) {
             $employee = $this->model_->where('user_id', auth()->user()->id)->first();
            if ($employee) {
                return $companyId = 1;
            } else {
                return $companyId = $employee->company_id;
            }
        }
    }

    public function getEmployeeNumber() {
        $employeeNumber = rand(111111, 999999);
        if($this->model_->where('employee_number', $employeeNumber)->first()) {
            return $this->getEmployeeNumber();
        }
        return $employeeNumber;
    }

    public function employeeList(int $companyId)
    {
         $employee = DB::table('employees as e')
        ->leftjoin('employments as em', 'em.employee_id', 'e.user_id')
        ->leftjoin('generic_designations as gd', 'em.position_id', 'gd.id')
        ->leftjoin('users as u', 'u.id', 'e.user_id')
        ->leftjoin('generic_teams as gt', 'em.team_id', 'gt.id')
        ->leftjoin('generic_locations as gl', 'em.location_id', 'gl.id')
        ->leftjoin('generic_work_schedules as ws', 'em.work_schedule_id', 'ws.id')
        ->where('u.company_id',$companyId)
        ->where('u.deleted_at','=',NULL)
        ->select(['e.id','u.first_name', 'u.id as user_id', 'u.last_name','u.email','u.company_id' , 'e.gender','e.about_me','u.status','e.birth_date', 'e.address', 'e.city', 'e.country', 'e.state', 'e.zip', 'e.phone', 'e.mobile', 'gd.name as designation', 'gt.name as team', 'gl.name as location', 'ws.name as work_schedule', 'em.employment_start_date', 'e.facebook_links', 'e.linkedin_links', 'e.twitter_links', 'e.about_me', 'e.birth_date', 'e.marital_status', 'e.nationality', 'e.personal_phone', 'e.personal_email', 'e.ec_full_name', 'ec_phone', 'ec_email', 'ec_relationship', 'e.employee_number','e.created_at']);
        return $employee;
    }

    public function checkVerification($request)
    {
        return User::where('email', $request->email)->where('email_verified', 1)->first();
    }

    public function registerEmployeeUsingCode($code ,$Email ,$companyId)
    {
            $user = new User();
            $user->email = $Email;
            $user->password = Hash::make($Email);
            $user->company_id = $companyId;
            $user->save();
            $employee = new Employee();
            $employee->employee_number = $code;
            $employee->user_id = $user->id;
            $employee->save();
            return $employee;
        // }
    }

    public function verifyEmployeeEmailByToken($request)
    {
        try {
            $data = $request->all();
            $employee = User::where('email_verification_token', $data['token'])->first();
            $tokenCreatedAt = new \DateTime($employee->email_verification_token_created_at);
            $now = new \DateTime();
            if ($now->diff($tokenCreatedAt)->format('%i') > 60) {
                return $this->failed(['error' => EmployeeController::VERIFY_EMAIL_ERROR_TOKEN]);
            } else {
                    $employee->setEmailVerified();
                // event(new VerificationEmailByTokenEvent($employee->first_name, $employee->email));
                // return $this->created(['message' => EmployeeController::VERIFY_EMAIL_SUCCESS]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

   
}