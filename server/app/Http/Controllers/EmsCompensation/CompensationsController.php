<?php

namespace App\Http\Controllers\EmsCompensation;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmsCompensation\Requests\CreateCompensationRequest;
use App\Http\Controllers\EmsCompensation\Models\Compensation as ThisModel;
use App\Http\Controllers\EmsCompensation\Models\Compensation;
use App\Http\Controllers\EmsCompensation\Requests\UpdateCompensationRequest;
use App\Http\Controllers\EmsEmployee\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\UserService;
use Carbon\CarbonPeriod;
use DateTime;

class CompensationsController extends Controller
{

    const MODULE_NAME = 'compensation';
    const COMPENSATION_CREATED = 'New compensation created successfully';
    const COMPENSATION_NOT_CREATED = 'Error in creating a compensation';
    const COLLECTION_NAME = 'compensation';
    const COMPENSATION_UPDATED = 'Compensation updated successfully';
    const COMPENSATION_NOT_UPDATED = 'Error in updating compensation';
    const COMPENSATION_DELETED = 'compensation deleted successfully';
    const COMPENSATION_NOT_DELETED = 'Error in deleting compensation';
    const COMPENSATION_START_ALREADY_MARKED = 'Base Compensation record cannot overlap';
    const COMPENSATION_END_ALREADY_MARKED = 'End Date overlaps with another compensation record';
    private $userService_;

    public function __construct(ThisModel $model, UserService $userService)
    {
        parent::__construct($model);
        $this->userService_ = $userService;
    }
    public function index(Request $request)
    {
        try {
                $compensation = $this->model
                ->with(['employee.user'])->when(!empty($request->currency), function ($query) use ($request) {
                    return $query->where('currency', $request->currency);
                })
                ->when(!empty($request->effective_date), function ($query) use ($request) {
                    return  $query->where('effective_date', '=', $request->effective_date);
                })
                ->when(!empty($request->amount), function ($query) use ($request) {
                    return $query->where('amount', $request->amount);
                })
                ->where('company_id', '=', $this->userService_->getCompanyId($this->userId()))
                ->orderBy($this->getSortBy(), $this->getSort())
                ->paginate($this->getPerPage());
                if ($compensation->isNotEmpty()) {
                    return $this->created([CompensationsController::COLLECTION_NAME => $compensation]);
                }
                return $this->noRecord(['message' => CompensationsController::RECORD_NOT_FOUND], 200);

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function store(CreateCompensationRequest $request)
    {
        try {
            $data = $request->all();
            $datesDiff = [];
            $from=$request->effective_date;
            $to=$request->end_date;
            $userCompensation = DB::table('compensations')->select(['date_difference'])->where('employee_id', $request->employee_id)->get();
            if($userCompensation->isNotEmpty()){
                foreach ($userCompensation as $compensation) {
                    $decodedValue = json_decode($compensation->date_difference);
                    foreach($decodedValue as $singleData){
                        if($singleData == $from || $singleData == $to){
                            return $this->created(['message' => CompensationsController::COMPENSATION_START_ALREADY_MARKED]);
                        }
                    }
                }
            }
            if(empty($to)){
                array_push($datesDiff, $from);
              $data['date_difference'] = json_encode($datesDiff);
            }else{
              $period = CarbonPeriod::create($from, $to);
                foreach ($period as $date) {
                    array_push($datesDiff, $date->format('Y-m-d'));
                }
                $data['date_difference'] = json_encode($datesDiff);
            }
                    $this->model->fill($data);
                    $this->model->company_id = $this->companyId();
                    if ($this->model->save()) {
                      return $this->created(['message' => CompensationsController::COMPENSATION_CREATED]);
                    }  
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function show($compensationId)
    {
        try {
            $compensation = $this->model->find($compensationId);
            if (!$compensation) {
                return $this->created(['message' => CompensationsController::RECORD_NOT_FOUND],200);
            }
            return $this->created([CompensationsController::MODULE_NAME => $compensation]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateCompensationRequest $request)
    {
        
        try {
            $compensation_record= $this->model::find($request->id);
            if ($compensation_record == null) {
              return $this->created(['message' => CompensationsController::RECORD_NOT_FOUND],200);
            }
             $employee = Employee::where('id',$compensation_record->employee_id)->exists();
            if ($employee == null)
            {
                return $this->created(['message' => CompensationsController::RECORD_NOT_FOUND],200);
            }
            $updatedCompensation = $this->model::where('id', $request->id)->update($request->all());

            if ($updatedCompensation)
            {
                return $this->created(['message' => CompensationsController::COMPENSATION_UPDATED]);
            }
                return $this->failed(['message' => CompensationsController::COMPENSATION_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function destroy($id)
    {
        try {
            $result = $this->model->find($id);
            if($result){
                $result->delete();
                return $this->created(['message' => CompensationsController::COMPENSATION_DELETED]);
            }
            return $this->created(['message' => CompensationsController::RECORD_NOT_FOUND],200);
           
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
