<?php
namespace App\Http\Services;

use Exception;
use App\Events\WorkflowEvent;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\People\Models\Person;
use App\Http\Controllers\People\PeopleController;
use App\Http\Controllers\Modules\Enums\ModuleEnum;
use App\Http\Controllers\UserGroup\Models\UserGroup;
use App\Http\Controllers\EmsEmployee\Models\Employee;
use App\Http\Controllers\EmsEmployee\EmployeeController;

class EmployeesRegisterService extends PeopleController{

    private $model_;
    private $defaultGroupId_ = 3;
    private $mediaService_;

    public function __construct(Employee $employeeModel,
                                MediaService $mediaService) {
        $this->model_ = $employeeModel;
        $this->mediaService_ = $mediaService;
    }

    public function employeeRegister($request , $employeeService ,$userService)
    {
        try {
            $data = $request->all();
            $user = registerUser($data['first_name'], $data['last_name'], $data['email'], $data['password'], EmployeeController::MODULE_NAME,true, $this->companyId());
            $data['employee_number'] = ($request->has('employee_number') && $request->get('employee_number') != NULL)? $request->get('employee_number'): $employeeService->getEmployeeNumber();
            $data['user_id'] = $user->id;
            $this->model_ = $this->model_->fill($data);
            $this->model_ = $this->preRegistration($this->model_);
            if ($this->model_->save()) {
                if ($request->has('profile_picture')) {
                    $this->setProfilePicture('profile_picture', $request);
                }

            $this->postRegistrationn($user ,$userService);
            // event(new SignupSuccessfullEvent($request->first_name, $request->email,$this->model_->email_verification_token));
               return $this->created(['message' => PeopleController::SIGNUP_SUCCESS, 'profile' => $this->model_]);
            }
            return $this->failed(['error' => PeopleController::SIGNUP_ERROR]);
        } catch (Exception $ex) {
           return $this->serverError($ex);
       }

    }
    protected function preRegistration(Person $model)
    {
        return $model;
    }

    private function postRegistrationn(Person $model ,$userService)
    {
        $userService->getEmployeeEmailVerificationToken($model);
        $EmployeeGroup = new UserGroup();
        $EmployeeGroup->user_id = $model->id;
        $EmployeeGroup->group_id = $this->defaultGroupId_;
        $EmployeeGroup->save();
        event(new WorkflowEvent(getModuleIdFromEntity(new User()), $model, 'create'));
        return $model;
    }

    public function setProfilePicture($filename, $request) {
        $request->merge(['module_id' => ModuleEnum::Employees]);
        $request->merge(['media_type' => 6]);
        $request->merge(['relation_id' => $this->model_->id]); 
        return $this->mediaService_->saveMedia($filename, $request);
    }

}
