<?php
namespace App\Http\Services;

use App\Http\Controllers\Employments\EmploymentsController;
use App\Http\Controllers\Employments\Models\Employment;
use App\Http\Controllers\People\PeopleController;
use Exception;

class EmploymentService extends PeopleController{
    
    private $employment_;
    
    public function __construct(Employment $employment) {
        $this->employment_ = $employment;
        $this->model_ = $employment;
    }
  
    public function employments(int $companyId){
        return $this->employment_->from('employees as e')
        ->leftjoin('employments as em','e.id','=','em.employee_id')
        ->leftjoin('users as u','e.user_id','=','u.id')
        ->leftJoin('generic_work_schedules as gws','em.work_schedule_id','gws.id')
        ->leftJoin('generic_designations as gd','em.position_id','gd.id')
        ->leftJoin('generic_teams as gt','em.team_id','gt.id')
        ->leftJoin('generic_locations as gl','em.location_id','gl.id')
        ->where('u.company_id',$companyId)
        ->select(['e.id','e.employee_number','u.first_name','gws.id as work_schedule_id','gws.name as work_schedule',
                  'gd.id as designaiton_id','gd.name as designation','gt.id as team_id',
                  'gt.name as team','gl.id as location_id',
                  'gl.name as location','em.created_at']);
    }

    public function updateEmployments($request)
    {
        try {  
            $employmentData = $request->all();
            $positionCheck = $this->checkEmployeePositionExist($employmentData,'new_position_id')->exists();
            if($positionCheck){
                if($this->updateEmployment($employmentData, true)){
                    $data = $this->checkEmployeePositionExist($employmentData,'position_id')->first();
                    return $this->created(['message' => EmploymentsController::EMPLOYMENT_UPDATED,'data' => $data]);
                }
                return $this->failed(['message' => EmploymentsController::EMPLOYMENT_NOT_AVAILABLE]);
            }         
            if($this->updateEmployment($employmentData, false)){
                $data = $this->checkEmployeePositionExist($employmentData,'new_position_id')->first();
                return $this->created(['message' => EmploymentsController::EMPLOYMENT_UPDATED, 'data' => $data]);
            }
            return $this->failed(['message' => EmploymentsController::EMPLOYMENT_NOT_AVAILABLE]);    
        }catch (Exception $ex) {
            // $this->addLog('info',$this->updated,$ex,EmploymentsController::MODULE_NAME,$this->type(),$this->userId());
            return $this->serverError($ex);
        }
    }

    
    public function checkEmployeePositionExist($employmentData){
        return $this->model_->where('employee_id', $employmentData['employee_id'])
               ->where('position_id', $employmentData['position_id']);
    }

    public function updateEmployment($employmentData, $flag){
        return $this->model_->where('employee_id', $employmentData['employee_id'])
        ->where('position_id', $employmentData['position_id'])->update([
            'probation_start'=> $employmentData['probation_start'],
            'probation_end'=> $employmentData['probation_end'],
            'employment_start_date'=> $employmentData['employment_start_date'],
            'work_schedule_id'=> $employmentData['work_schedule_id'],
            'location_id'=> $employmentData['location_id'],
            'team_id'=> $employmentData['team_id'],
            'position_id'=> $flag == true ? $employmentData['position_id'] : $employmentData['new_position_id'],
            ]);
    }

    public function storeEmployment($request)
    {
        $data = $request->all();
        try {
            $isPrimaryJobExist = $this->checkEmployeeUsingEmployeeId($data['employee_id'])->exists();
            if($isPrimaryJobExist){
                $isAlreadyHavePosition = $this->checkEmployeeUsingEmployeeId($data['employee_id'])->where('position_id', $data['position_id'])->exists();
                if ($isAlreadyHavePosition) {
                    return $this->created(['message' => EmploymentsController::EMPLOYMENT_POSITION_EXIST]);
                } 
                else {
                    $data['is_primary_job'] = 'No';
                    $recordCreated = $this->model_->create($data);
                    if ($recordCreated) {
                        return $this->created(['message' => EmploymentsController::EMPLOYMENT_CREATED]);
                    }  
                    return $this->failed(['message' => EmploymentsController::EMPLOYMENT_NOT_CREATED]);
                }
            }
            else{
                $recordCreated = $this->model_->create($data);
                if($recordCreated) {
                    return $this->created(['message' => EmploymentsController::EMPLOYMENT_CREATED]);
                }
                return $this->failed(['message' => EmploymentsController::EMPLOYMENT_NOT_CREATED]);
            }   
        }catch (Exception $ex) {
            $this->addLog('Error',$this->created,$ex->getMessage(),EMploymentsController::MODULE_NAME,'System','0');
            return $this->serverError($ex);
        }
    }

    public function checkEmployeeUsingEmployeeId($employeeId){
        return $this->model_->where('employee_id', $employeeId);
    }

    public function destroyEmployment($employeeId)
    {
        try {
             $employee = $this->model_->find($employeeId);
             if($employee){
              return  $employee->is_primary_job != 'Yes' ? $employee->delete()? $this->created(['message' => EmploymentsController::EMPLOYMENT_DELETED]):$this->failed(['message' => EmploymentsController::JOB_NOT_DELETED]) :$this->failed(['message' => EmploymentsController::JOB_NOT_DELETED]);
             }   
                return $this->noRecord(['message'=>EmploymentsController::RECORD_NOT_FOUND], 200);      
        } catch (Exception $ex) {
            $this->addLog('Error',$this->deleted,$ex->getMessage(),EmploymentsController::MODULE_NAME,'System','0');
            return $this->serverError($ex);
        }
    }

    public function getEmployee($data){
        return $this->model_->where('employee_id', $data['employee_id']);
    }


   

}