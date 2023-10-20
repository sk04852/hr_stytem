<?php

namespace App\Http\Controllers\TimeOff;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\TimeOffApprovedEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TimeOff\Models\EmployeeTimeOff as Thismodel;
use App\Http\Controllers\TimeOff\Requests\ApproveOrCancelRequest;
use App\Http\Controllers\TimeOff\Requests\CancelTimeOffRequest;
use App\Http\Controllers\TimeOff\Requests\CreateEmployeeTimeOffRequest;
use App\Http\Services\EmployeesService;
use App\Http\Services\EmployeeTimeOffService;
use App\Http\Controllers\TimeOff\Models\TimeoffType;
use App\Http\Services\UserService;
use App\Http\Resources\TimeoffRequests\TimeoffRequestsCollection;
use DateTime;

class EmployeeTimeOffsController extends Controller
{
    const MODULE_NAME = 'timeoff';
    const TIMEOFF_BALANCES = 'timeoff_balances';
    const TIMEOFF_CREATED = 'New timeoff created successfully';
    const TIMEOFF_REQUEST_UPDATED = 'Timeoff request has been updated';
    const TIMEOFF_NOT_CREATED = 'Error in creating a timeoff';
    const COLLECTION_NAME = 'timeoffs';
    const EMPLOYEE_TIMEOFF_REQUESTS = 'timeoff_requests';
    const TIMEOFF_UPDATED = 'Timeoff updated successfully';
    const TimeOFF_APPROVED = 'After approve or cancel you cannot update it';
    const TIMEOFF_NOT_UPDATED = 'Error in updating timeoff';
    const TIMEOFF_DELETED = 'Timeoff deleted successfully';
    const TIMEOFF_NOT_DELETED = 'Error in deleting timeoff';
    const TIMEOFF_ALREADY_MARKED = 'TIMEOFF overlap with existing date';
    const TIMEOFF_NOT_FOUND = 'Timeoff not found';
    const INVALID_TIMEOFF_ID = 'Timeoff not found, invalid timeoff id';
    const TIMEOFF_REQUEST_CANCEL = 'Timeoff request cancel successfully';
    const NO_TIMEOFF_REQUEST = 'You haven’t made any time off requests';

    private $employeeTimeOffService_;
    private $employeesService_;
    private $timeOffTypeModle_;
    private $userService_;


    public function __construct(ThisModel $model,
        TimeoffType $timeOffTypeModel,
        EmployeesService $employeesService,
        UserService $userService,
        EmployeeTimeOffService $employeeTimeOffService) {
            parent::__construct($model);
            $this->employeeTimeOffService_ = $employeeTimeOffService;
            $this->timeOffTypeModle_ = $timeOffTypeModel;
            $this->employeesService_ = $employeesService;
            $this->userService_ = $userService;
    }

    public function index()
    {
        try {

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function myTimeoffRequests(Request $request)
    {
        try {
            $userId = $this->userId();
            $employeeId = $this->userService_->getEmployeeId($userId);
            $records = $this->employeeTimeOffService_->getEmployeeTimeoffRequests($employeeId);
            return TimeoffRequestsCollection::collection($records);
            return $this->created([EmployeeTimeOffsController::EMPLOYEE_TIMEOFF_REQUESTS => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateEmployeeTimeOffRequest $request)
    {
        try {
            $data = $request->all();
            $userId = $this->userId();
            $data['employee_id'] = ($request->has('employee_id') && $request->get('employee_id')!= "")? $request->employee_id: $this->userService_->getEmployeeId($userId);
            $note = $request->get('note', '');
            $fromDate = Carbon::parse($data['from_date'])->startOfDay()->toDate();
            $toDate = Carbon::parse($data['to_date'])->endOfDay()->toDate();
            $data['company_id'] = $this->companyId();
            $employee = $this->employeesService_->getEmployee($data['employee_id']);
            $timeOffType = $this->timeOffTypeModle_->where('id', $data['timeoff_type_id'])->first();
            $absentDaysRequested = (float)$toDate->diff($fromDate)->format('%a');
            $companyId = $this->companyId();
            if($this->employeeTimeOffService_->canEmployeeRequestTimeOff($employee, $timeOffType, $fromDate, $toDate)) {
                $timeOff = [
                    'employee_id'=> $employee->id,
                    'timeoff_type_id'=> $timeOffType->id,
                    'from_date'=> $fromDate,
                    'to_date'=> $toDate,
                    'status'=> ($timeOffType->requires_approval == "No")? "Approved": "Pending",
                    'time_off_for_from'=> 'Full day',
                    'time_off_for_to'=> 'Full day',
                    'absent_days'=> $absentDaysRequested,
                    'note'=> $note,
                    'company_id' => $companyId,
                    'created_at'=> new Carbon(),
                    'updated_at'=> new Carbon()
                ];
                if($this->model->create($timeOff)) {
                    return $this->created([EmployeeTimeOffsController::MODULE_NAME => EmployeeTimeOffsController::TIMEOFF_CREATED]);
                }
            } else {
                return $this->notAllowed([EmployeeTimeOffsController::MODULE_NAME => 'You cannot request time off of this type, you might have no days left.']);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function updateTimeOffRequest(ApproveOrCancelRequest $request)    {
        try {
            $status = $request->status;
            $note = $request->get('admin_time_off_note', '');
            $timeOffRequest = $this->employeeTimeOffService_->getTimeOff($request->id);
            $user = $timeOffRequest->employee->user;
            if(!$timeOffRequest) {
                throw new Exception("Timeoff request is invalid");
            }
            $updatedData = [];
            if($status === "Approved") {
                $updatedData = [
                    'status'=> 'Approved',
                    'status_updated_at'=> new DateTime(),
                    'approved_by'=> $this->userId()
                ];
                $timeOffRequest->update($updatedData);
                event(new TimeOffApprovedEvent($timeOffRequest, $user, $status, $note));
            } else if ($status === "Cancelled") {
                $updatedData = [
                    'status'=> 'Cancelled',
                    'status_updated_at'=> new DateTime(),
                ];
                $timeOffRequest->update($updatedData);
            } else if ($status === "Declined") {
                $updatedData = [
                    'status'=> 'Declined',
                    'status_updated_at'=> new DateTime(),
                ];
                $timeOffRequest->update($updatedData);
            }
            return $this->created([EmployeeTimeOffsController::MODULE_NAME => EmployeeTimeOffsController::TIMEOFF_REQUEST_UPDATED]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function Cancel_timeoff_request(CancelTimeOffRequest $request)
    {
        try {
            $alldata = $request->all();
            $users = $this->model->where('id', $request->id)->first();
            if ($users) {
                if ($request->status === 'Cancelled') {
                    $this->model->find($request->id)->update($alldata);
                    return $this->created(['message' => EmployeeTimeOffsController::TIMEOFF_REQUEST_CANCEL]);
                }
            } else {
                return $this->noRecord(['message' => EmployeeTimeOffsController::INVALID_TIMEOFF_ID]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function user_time_off_requests(Request $request)
    {
        try {
            $employee = DB::table('time_offs as to')
           ->where('employee_id', $request->employee_id)
           ->where('time_off_type', $request->time_off_type)
            ->get(['to.from_date', 'to.to_date', 'to.status']);
            foreach ($employee as $emp) {
                $leave_days = Carbon::parse($emp->to_date)->diffInDays(Carbon::parse($emp->from_date));
                $first = $leave_days;
            }
            if ($employee->isEmpty()) {
                return $this->created([EmployeeTimeOffsController::NO_TIMEOFF_REQUEST]);
            } else {
                return $this->created([EmployeeTimeOffsController::COLLECTION_NAME => $employee]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function timeOffBalances(Request $request)
    {
        try {
            $balances = [];
            $employee = $this->userService_->getEmployeeByUserId($this->userId());
            if($employee) {
                $balances = $this->employeeTimeOffService_->getEmployeeTimeOffBalances($employee);
            }
            return $this->created([EmployeeTimeOffsController::TIMEOFF_BALANCES=> $balances]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function adminTimeOffRequests(Request $request)
    {
        try {
            $companyId = $this->companyId();
            $records = $this->employeeTimeOffService_->getTimeoffRequests(['company_id'=> $companyId, 'status'=> 'Pending']);
            return TimeoffRequestsCollection::collection($records);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
