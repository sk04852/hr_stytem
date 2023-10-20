<?php
namespace App\Http\Services;

use App\Http\Controllers\Employments\EmploymentsController;
use App\Http\Controllers\Employments\Models\Employment;
use App\Http\Controllers\People\PeopleController;
use Exception;
use Illuminate\Support\Facades\DB;

class EmploymentJobService extends PeopleController{
        
    public function __construct(Employment $employment) {
        $this->employment_ = $employment;
        $this->model_ = $employment;
    }


    public function switchPrimaryJob($request ,$employmentService){
        try {
            $employmentData =  $request->all();
            $positionCheck = $employmentService->checkEmployeePositionExist($employmentData)->exists();
             if($positionCheck){
               $employmentService->getEmployee($employmentData)->where('is_primary_job','yes')->update(['is_primary_job' => 'No']);
               $employmentService->getEmployee($employmentData)->where('position_id',$request->position_id)->update(array('is_primary_job' => 'Yes' ));
               return $this->created(['message' => EmploymentsController::EMPLOYMENT_UPDATED]);
             }
            return $this->noRecord(['message' => EmploymentsController::EMPLOYMENT_NOT_AVAILABLE]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
        
    }

    
    public function getPrimaryJob($request ,$employmentService)
    {
        try {
             $primaryJob =  $employmentService->checkEmployeeUsingEmployeeId($request->employee_id)->where('is_primary_job','Yes')->first();
            if (!$primaryJob) {
                return $this->noRecord(['message'=>EmploymentsController::RECORD_NOT_FOUND], 200);
            }
            return $this->created([EmploymentsController::MODULE_NAME => $primaryJob]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

}