<?php
namespace App\Http\Services;

use App\Http\Controllers\EmsEmployee\EmployeeController;
use App\Http\Controllers\EmsEmployee\Models\Employee;
use App\Http\Controllers\People\Interfaces\IPerson;
use App\Http\Controllers\People\Models\Person;
use App\Http\Controllers\People\PeopleController;
use App\Http\Controllers\Users\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeesPasswordResetService extends PeopleController{
    
    private $model_;
    private $userService_;
    private $defaultWorkScheduleId_ = 4;
    
    public function __construct(Employee $employeeModel, 
                                UserService $userService) {
        $this->model_ = $employeeModel;
        $this->userService_ = $userService;
    }
    public function forgotEmployeePassword($request)
    {
        try {
            $data = $request->all();
            $user = User::where('email', $data['email'])->first();
            if ($user) {
                $Data = [
                    'full_name' => $user->getFullName(),
                    'token' => Str::random(12)
                ];
                $user->setPasswordRecoveryToken($Data['token']);
                return $this->created(['message' => PeopleController::FORGOT_SUCCESS]);
            } else {
                return $this->failed(['error' => PeopleController::FORGOT_ERROR]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function resetEmployeePassword($request)
    {
        try {
            $data = $request->all();
            $data['password'] = Hash::make($data['password']);
            $User = User::where('password_recovery_token', $data['token'])->first();
            $tokenCreatedAt = new \DateTime($User->password_recovery_token_created_at);
            $now = new \DateTime();

            if ($now->diff($tokenCreatedAt)->format('%i') > 60) {
                return $this->failed(['error' => PeopleController::RESET_ERROR_TOKEN]);
            }

            if ($User->setPassword($data['password'])) {

                $this->notify($this->model);
                return $this->created(['message' => PeopleController::RESET_SUCCESS]);
            } else {
                return $this->failed(['error' => PeopleController::RESET_ERROR]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function changeEmployeePasswordByAdmin($request)
    {
        try {
            $allData = $request->all();
            $employee = Employee::where('id', $allData['id'])->first();
            if (!$employee) {
                return $this->created(['message' => 'Employee not Exist']);
            }
            $allData['password'] = Hash::make($allData['password']);
            changeUserPassword($employee->user->email, $allData['password'], false);
            $updatedEmployee = $this->model_->find($allData['id'])->update($allData);
            if ($updatedEmployee) {
                return $this->created(['message' => EmployeeController::EMPLOYEE_PASSWORD_UPDATED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function updatePassword($request)
    {
        try {
            $employee = Employee::where('id', $request->id)->exists();
            if (!$employee) {
                return $this->created(['message' => 'Employee not Exist']);
            }
            else
            {
                $user = Employee::where('id',$request->id)->first();
                 $employee = User::where('id', $user->user_id)->first();
                if ($employee->email_verified==0) {
                    return $this->created(['message' => EmployeeController::EMAIL_NOT_VERIFIED]);
                } else {
                    if (!Hash::check($request->current_password, $employee->password)) {
                        return $this->created(['message' => EmployeeController::UNAUTHORIZED]);
                    } else {

                        $hashedVersion = Hash::make($request->new_password);
                        User::where('id', $user->user_id)->update(['password' => $hashedVersion,'confirm_password'=>$hashedVersion ]);
                        return $this->created(['message' => EmployeeController::EMPLOYEE_UPDATED]);
                    }
                }
            }

        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
   
}