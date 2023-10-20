<?php

namespace App\Http\Controllers\EmployeesPolicies;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\EmployeesPolicies\Models\EmployeePolicy as ThisModel;
use App\Http\Controllers\EmployeesPolicies\Requests\CreateEmployeePolicyRequest as CreateRequest;
use App\Http\Controllers\EmployeesPolicies\Requests\UpdateEmployeePolicyRequest as UpdateRequest;

class EmployeesPoliciesController extends Controller
{
    const MODULE_NAME = 'employees_policies';
    const COLLECTION_NAME = 'employees_policies';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        try {
            $records = DB::table('employees_policies as ep')
            ->join('employees as e', 'ep.employee_id', 'e.id')
            ->join('policies as p', 'ep.policy_id', 'p.id')
            ->get(['e.employee_number', 'e.first_name', 'e.last_name', 'e.email', 'e.password', 'e.password_confirmation', 'e.address', 'e.city', 'e.country', 'e.code', 'e.state', 'e.zip', 'e.image', 'e.phone', 'e.mobile', 'e.designation_id', 'e.team_id', 'e.location_id', 'e.job_type_id', 'e.work_schedule_id', 'e.employment_start', 'e.salary', 'e.facebook_links', 'e.linkedin_links', 'e.twitter_links', 'e.birth_date', 'e.marital_status', 'e.nationality', 'e.personal_phone', 'e.personal_email', 'e.gender', 'e.ec_full_name', 'e.ec_phone', 'e.ec_email', 'e.ec_relationship', 'p.policy_name']);
            return $this->created([EmployeesPoliciesController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function create()
    {
        return;
    }

    public function store(CreateRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if($this->model->save()) {
                $this->addLog('info',$this->created,$this->model,EmployeesPoliciesController::MODULE_NAME,$this->type(),$this->userId());
                return $this->created([EmployeesPoliciesController::MODULE_NAME => $this->model, 'message'=> EmployeesPoliciesController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {   
            $this->addLog('Error',$this->created,$ex->getMessage(),EmployeesPoliciesController::MODULE_NAME,'System','0');
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->findOneById($id);
            return $this->created([EmployeesPoliciesController::MODULE_NAME => $record]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function edit($id)
    {
        return;
    }

    public function update(UpdateRequest $request)
    {
        try {
            $record = $this->findOneById($request->id);
            $record->fill($request->all());
            if($record->save()) {
                $this->addLog('info',$this->updated,$record,EmployeesPoliciesController::MODULE_NAME,$this->type(),$this->userId());
                return $this->created([EmployeesPoliciesController::MODULE_NAME => $record, 'message'=> EmployeesPoliciesController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            $this->addLog('Error',$this->updated,$ex->getMessage(),EmployeesPoliciesController::MODULE_NAME,'System','0');
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        return;
    }


}
