<?php

namespace App\Http\Controllers\Employments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Employments\Models\Employment as ThisModel;
use App\Http\Controllers\Employments\Requests\CreateEmploymentRequest;
use App\Http\Controllers\Employments\Requests\GetPrimaryJobRequest;
use App\Http\Controllers\Employments\Requests\SwitchprimaryJobRequest;
use App\Http\Controllers\Employments\Requests\UpdateEmploymentRequest;
use App\Http\Services\EmploymentService;
use App\Http\Services\EmploymentJobService;

use Exception;

class EmploymentsController extends Controller
{
    const MODULE_NAME = 'employment';
    const COLLECTION_NAME = 'employments';
    const EMPLOYMENT_CREATED = 'New employment created successfully';
    const EMPLOYMENT_POSITION_EXIST = 'You already have this position';
    const EMPLOYMENT_NOT_CREATED = 'Error in creating a employment';
    const EMPLOYMENT_UPDATED = 'Employment updated successfully';
    const EMPLOYMENT_NOT_UPDATED = 'Error in updating employment';
    const EMPLOYMENT_DELETED = 'Employment deleted successfully';
    const EMPLOYMENT_NOT_DELETED = 'Error in deleting employment';
    const JOB_NOT_DELETED = 'Primary job not able to delete.First switch any other job to primary';
    const EMPLOYMENT_NOT_AVAILABLE = 'No record Available';
    private $employmentService_;
    private $employmentJobService_;


    public function __construct(ThisModel $model, EmploymentService $employment ,EmploymentJobService $employmentJobService)
    {
        parent::__construct($model);
        $this->employmentService_ = $employment;
        $this->employmentJobService_ = $employmentJobService;

    }

    public function index()
    {
        try{
            $employments =  $this->employmentService_->employments($this->companyId())->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            if($employments->isNotEmpty()){
                return $this->created([EmploymentsController::COLLECTION_NAME => $employments]);
            }
            return $this->created(['message'=>EmploymentsController::RECORD_NOT_FOUND]);      
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function store(CreateEmploymentRequest $request)
    {
        try {
            return $this->employmentService_->storeEmployment($request); 
        }catch (Exception $ex) {
            $this->addLog('Error',$this->created,$ex->getMessage(),EMploymentsController::MODULE_NAME,'System','0');
            return $this->serverError($ex);
        }
    }


     public function update(UpdateEmploymentRequest $request)
    {
        try {  
           return $this->employmentService_->updateEmployments($request);  
        }catch (Exception $ex) {
            // $this->addLog('info',$this->updated,$ex,EmploymentsController::MODULE_NAME,$this->type(),$this->userId());
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try {
            return $this->employmentService_->destroyEmployment($id);
        } catch (Exception $ex) {
            $this->addLog('Error',$this->deleted,$ex->getMessage(),EmploymentsController::MODULE_NAME,'System','0');
            return $this->serverError($ex);
        }
    }
   
  
     public function switchPrimaryJob(Request $request){
        try {
            return $this->employmentJobService_->switchPrimaryJob($request ,$this->employmentService_);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
        
    }



    public function getPrimaryJob(GetPrimaryJobRequest $request)
    {
        try {

           
            return $this->employmentJobService_->getPrimaryJob($request ,$this->employmentService_);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}