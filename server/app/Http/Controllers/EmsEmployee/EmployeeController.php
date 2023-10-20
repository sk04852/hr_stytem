<?php

namespace App\Http\Controllers\EmsEmployee;

use Exception;
use Illuminate\Http\Request;
use App\Http\Services\UserService;
use Illuminate\Support\Facades\DB;
use App\Http\Services\MediaService;
use App\Http\Services\EmailsService;
use Illuminate\Support\Facades\Hash;
use App\Http\Services\CompaniesService;
use App\Http\Services\EmployeesService;
use App\Http\Controllers\Users\Models\User;
use App\Http\Services\EmployeesRegisterService;
use App\Http\Controllers\People\PeopleController;
use App\Http\Resources\Employees\EmployeesProfile;
use App\Http\Services\EmployeesPasswordResetService;
use App\Http\Controllers\EmsEmployee\Models\Employee;
use App\Http\Controllers\EmsEmployee\Requests\EmployeeLoginRequest;
use App\Http\Controllers\EmsEmployee\Requests\ChangePasswordRequest;
use App\Http\Controllers\EmsEmployee\Requests\CreateEmployeeRequest;
use App\Http\Controllers\EmsEmployee\Requests\UpdateEmployeeRequest;
use App\Http\Controllers\EmsEmployee\Requests\CreateEmployeeByEmailRequest;
use App\Http\Controllers\EmsEmployee\Requests\EmployeeResetPasswordRequest;
use App\Http\Controllers\EmsEmployee\Requests\UpdatePasswordByAdminRequest;
use App\Http\Controllers\EmsEmployee\Requests\EmployeeForgotPasswordRequest;
use App\Http\Controllers\EmsEmployee\Requests\ResendEmailVerificationRequest;
use App\Http\Controllers\EmsEmployee\Requests\VerifyEmployeeEmailByTokenRequest;

class EmployeeController extends PeopleController
{
    const MODULE_NAME = 'employee';
    const COLLECTION_NAME = 'employees';
    const EMPLOYEE_REGISTERED = 'Employee has been registered successfully';
    const EMPLOYEE_NOT_REGISTERED = 'Employee not registered';
    const EMPLOYEE_DELETED = 'Employee has been deleted successfully';
    const EMPLOYEE_UPDATED = 'Employee has been updated successfully';
    const EMPLOYEE_NOT_FOUND = 'Employee not found';
    const EMPLOYEES_NOT_FOUND = 'Employees not found';
    const EMPLOYEE_RESEND_TOKEN = 'Token successfully resent';
    const INVALID_EMPLOYEE_ID = 'Employee not found, invalid employee id';
    const EMPLOYEE_ALREADY_VERIFIED = 'Employee already verified';
    const UNAUTHORIZED = 'Login credentials does not match';
    const LOGIN_SUCCESS = 'Logged in Successfully';
    const EMPLOYEE_PASSWORD_UPDATED = 'Password Updated Successfully';
    const COMPANY_NOT_FOUND = 'Company Not Found';
    const EMAIL_EXISTS = 'This email already exists';
    const EMAIL_NOT_VERIFIED = 'you are not verify email yet';
    const EMPLOYEE_NUMBER_NOT_MATCH = 'Employee number not match';


    private $employeesService_;
    private $employeePasswordResetService_;
    private $userService_;
    private $employeesRegisterService_;
    public function __construct(Employee $model,
                                EmailsService $emailsService,
                                EmployeesService $employeesService,
                                CompaniesService $companiesService,
                                UserService $userService,
                                EmployeesRegisterService $employeesRegisterService,
                                EmployeesPasswordResetService $employeePasswordResetService)
    {
        parent::__construct($model, $emailsService);
        $this->model = $model;
        $this->companiesService_ = $companiesService;
        $this->userService_ = $userService;
        $this->employeesRegisterService_ = $employeesRegisterService;
        $this->employeesService_ = $employeesService;
        $this->employeePasswordResetService_ = $employeePasswordResetService;
    }

    public function employeeRegister(CreateEmployeeRequest $request)
    {
        try {
           return  $this->employeesRegisterService_->employeeRegister($request, $this->employeesService_ ,$this->userService_ );
        } catch (Exception $ex) {
           return $this->serverError($ex);
       }

    }

    public function employeeList(Request $request)
    {
        try {
            $employeeList = $this->employeesService_->employeeList($this->companyId());
            if ($employeeList != NULL) {
            $employeeList = $this->model->employeeFilters($request, $employeeList)->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
                    return $this->created([EmployeeController::COLLECTION_NAME => $employeeList]);
                }
            return $this->noRecord([EmployeeController::EMPLOYEE_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function verifyEmployeeEmailByToken(VerifyEmployeeEmailByTokenRequest $request)
    {
        try {
             $this->employeesService_->verifyEmployeeEmailByToken($request);
            return $this->created(['message' => EmployeeController::VERIFY_EMAIL_SUCCESS]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function resendEmailVerificationToken(ResendEmailVerificationRequest $request)
    {
        try {
            $data = $request->all();
            $userEmail = User::where('email', $data['email'])->first();
            if (($userEmail->email_verified) == 1) {
                return $this->created(['message' => EmployeeController::EMPLOYEE_ALREADY_VERIFIED]);
            }
            $this->userService_->getEmployeeEmailVerificationToken($userEmail);
            // event(new ResendVerificationTokenEvent($userEmail->email_verification_token, $userEmail->email));
                return $this->created(['Employee' => $userEmail, 'message' => EmployeeController::EMPLOYEE_RESEND_TOKEN]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function login(EmployeeLoginRequest $request)
    {
        try {
            $data = $request->all();
            $employee = $this->employeesService_->checkVerification($request);
            if (!$employee) {
                return $this->created(['message' => EmployeeController::UNAUTHORIZED]);
            }
            if (!Hash::check($data['password'], $employee->password)) {
                return $this->created(['message' => EmployeeController::UNAUTHORIZED]);
            }
            $company = DB::table('companies')->where('id', '=', $employee->company_id)->first();
            return $this->created(['message' => EmployeeController::LOGIN_SUCCESS, EmployeeController::MODULE_NAME => $employee, 'company' => $company]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function updateEmployee(UpdateEmployeeRequest $request)
    {
        try {
            $userTableData = [
                'email',
                'first_name',
                'last_name'
            ];
            $allData = $request->except($userTableData);
            $userData = $request->only($userTableData);
            $employee = Employee::where('id', $allData['id'])->first();
            if (!empty($allData['profile_picture'])) {
                $this->employeesRegisterService_->setProfilePicture('profile_picture', $request);
            }
            if (!$employee) {
                return $this->created(['message' => "Employee doesn't exist"]);
            }

            if($this->model->find($allData['id'])->update($allData)) {
                $employee->user->update($userData);
                return $this->created(['message' => EmployeeController::EMPLOYEE_UPDATED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function UpdateEmployeeUsingEmployeeNumber(Request $request)
    {
        try {
                $allData = $request->all();
                $employee = Employee::where('id',$allData['id'])->first();
                if($employee)
                {
                    $dbEmployeeCode = $this->model->where('id', $allData['id'])->pluck('employee_number')->first();
                    if ($request->employee_number == $dbEmployeeCode) {
                    $allData['password'] = Hash::make($allData['password']);
                    $allData['password_confirmation'] = $allData['password'];
                    User::where('id' ,$employee->user_id)->update(['password' => $allData['password']]);
                        $this->model->find($request->id)->update($allData);
                    return $this->created(['message' => EmployeeController::EMPLOYEE_UPDATED]);
                    }
                    else
                    {
                    return $this->created(['message' => EmployeeController::EMPLOYEE_NUMBER_NOT_MATCH]);
                    }
                }
                return $this->created(['message' => EmployeeController::EMPLOYEE_NOT_FOUND]);
        }
        catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function forgotEmployeePassword(EmployeeForgotPasswordRequest $request)
    {
        try {
            return $this->employeePasswordResetService_->forgotEmployeePassword($request);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function resetEmployeePassword(EmployeeResetPasswordRequest $request)
    {
        try {
            return $this->employeePasswordResetService_->resetEmployeePassword($request);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function changeEmployeePasswordByAdmin(UpdatePasswordByAdminRequest $request)
    {
        try {
            return $this->employeePasswordResetService_->changeEmployeePasswordByAdmin($request);
        }
         catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function updatePassword(ChangePasswordRequest $request)
    {
        try {
            return $this->employeePasswordResetService_->updatePassword($request);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(int $id)
    {
        try {
            $employee = $this->model->find($id);
            if ($employee) {
                    if($this->userService_->belongsToGroup($employee->user->id, env("APPLICATION_ADMIN_GROUP_ID")))
                    throw new Exception("System Administrators cannot be deleted");

                $user= User::where('id',$employee->user_id);
                $employee->delete();
                $user->delete();
                return $this->created(['message' => EmployeeController::EMPLOYEE_DELETED]);
            }
            return $this->noRecord(['message' => EmployeeController::EMPLOYEE_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function generateCodeUsingEmail(CreateEmployeeByEmailRequest $request)
    {
        try {
            $code = $this->employeesService_->getEmployeeNumber($request);
            $Email = $request->email;
            $companyId = $this->companyId();
            $created = $this->employeesService_->registerEmployeeUsingCode($code , $Email, $companyId);
            if ($created) {
                // event(new EmployeeCodeForSignupEvent($Email, $code));
                return $this->created(['message' => EmployeeController::EMPLOYEE_REGISTERED]);
            }
            else {
                return $this->created(['message' => EmployeeController::EMPLOYEE_NOT_REGISTERED]);

            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function employeeProfile(){
        $data = User::with('employee')->find($this->userId());
        if($data){
            return new EmployeesProfile($data);
        }
        return $this->noRecord(['message' => EmployeeController::RECORD_NOT_FOUND], 200);
    }

}
