<?php

namespace App\Http\Controllers\EmsAttendance;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmsEmployee\Models\Employee;
use App\Http\Controllers\EmsAttendance\Models\EmployeeAttendace as Thismodel;
use App\Http\Controllers\EmsAttendance\Requests\CreateEmployeeAttendaceRequest;
use App\Http\Services\AttendanceService;
use App\Http\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AttendanceController extends Controller
{
    const MODULE_NAME = 'attendance';
    const ATTENDANCE_CREATED = 'New attendance created successfully';
    const ATTENDANCE_NOT_CREATED = 'Error in creating a attendance';
    const COLLECTION_NAME = 'attendance';
    const ATTENDANCE_UPDATED = 'Attendance updated successfully';
    const ATTENDANCE_NOT_UPDATED = 'Error in updating attendance';
    const ATTENDANCE_DELETED = 'Attendance deleted successfully';
    const ATTENDANCE_NOT_DELETED = 'Error in deleting attendance';
    const ATTENDANCE_ALREADY_MARKED = 'Attendance already marked';
    const ATTENDANCE_NOT_FOUND = 'Attendance not found';
    const INVALID_EMPLOYEE_ID = 'Attendance not found, invalid employee id';


    private $employeeModel_;
    private $attendanceService_;
    private $userService_;

    public function __construct(ThisModel $model, 
                                Employee $employeeModel, 
                                AttendanceService $attendanceService,
                                UserService $userService)
    {
        parent::__construct($model);
        $this->employeeModel_ = $employeeModel;
        $this->attendanceService_ = $attendanceService;
        $this->userService_ = $userService;
    }

    public function index()
    {
        try {
            $userId = request()->user_id;
            $employeeId = $this->userService_->getEmployeeId($userId);
            $fromDate = null;
            $endDate = null;
            $type = strtolower((request()->has('type'))? request()->type: 'Monthly');
            $attendanceStatement = $this->attendanceService_->getEmployeeAttendanceStatement($employeeId, $type);
            if($type === 'monthly') {
                $fromDate = new Carbon('first day of this month');
                $endDate = new Carbon('last day of this month');
            } else if($type === 'weekly') {
                $fromDate = new Carbon('first day of this week');
                $endDate = new Carbon('last day of this week');
            }

            $attendances = $this->model->where('employee_id', $employeeId)->whereDate('date','>=',$fromDate)->whereDate('date', '<=', $endDate)->orderBy('date', 'ASC')->orderBy('start_time', 'ASC')->get();
            $attendanceDayMap = [];
            foreach($attendances as $attendance) {
                $start_time = new Carbon($attendance->start_time);
                $end_time = new Carbon($attendance->end_time);
                $attendance->start_time = $start_time->format('H:i');
                $attendance->end_time = $end_time->format('H:i');
                if(isset($attendanceDayMap[$attendance->date])) {
                    $attendanceDayMap[$attendance->date][] = $attendance;
                } else {
                    $attendanceDayMap[$attendance->date] = [$attendance];
                }
            }

            return $this->created([AttendanceController::MODULE_NAME => $attendanceDayMap, 'statement'=> $attendanceStatement]);

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateEmployeeAttendaceRequest $request) {
        try {

            $employeeId = $request->employee_id;
            $employeeAttendace = new Thismodel();
            $employeeAttendace->date = $request->date;
            $employeeAttendace->entry_type = $request->entry_type;
            $employeeAttendace->start_time = $request->start_time;
            $employeeAttendace->end_time = $request->end_time;
            $employeeAttendace->note = $request->note;
            $alreadyMarked = $this->attendanceService_->isAttendenceAlreadyMarked($employeeId, $employeeAttendace->date, $employeeAttendace->entry_type, $employeeAttendace->start_time, $employeeAttendace->end_time);
            if($alreadyMarked) {
                throw new Exception("Attendance for this time period has already been marked, please choose different times.");
            }
            $client = $this->employeeModel_->where('id', $employeeId)->first();

            if($client->attendance()->save($employeeAttendace)) {
                $this->addLog('info',$this->created,$client,AttendanceController::MODULE_NAME,$this->type(),$this->userId());
                return $this->created(['message'=> 'Attendance marked']);
            }

        } catch (Exception $ex) {
            $this->addLog('Error',$this->created,$ex->getMessage(),AttendanceController::MODULE_NAME,'System','0');
            return $this->serverError($ex);
        }
    }

    public function profile(int $employeeId)
    {
        try {
            $total_hour = DB::table('attendances')->where('employee_id', $employeeId)->get()->sum('total_hours');
            $employee = DB::table('attendances as a')
            ->leftjoin('employees as e', 'a.employee_id', 'e.id')
            ->where('a.employee_id', $employeeId)
            ->get(['e.id', 'e.first_name', 'e.last_name', 'e.email', 'e.address', 'e.city', 'e.phone', 'e.mobile', 'a.date', 'a.time_in', 'a.time_out', 'a.break_start', 'a.break_end', 'a.overtime']);
            $employee['total_hours'] = $total_hour;
            if ($employee->isNotEmpty()) {
                return $this->created(['employees' => $employee]);
            }
            return $this->noRecord(['message' => AttendanceController::INVALID_EMPLOYEE_ID], 200);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return $this->noRecord(['message' => AttendanceController::INVALID_EMPLOYEE_ID]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function markAttendanceUsingEmployeeCode(int $employeeCode, string $type) {
        try {
            $response = $this->attendanceService_->markAttendanceUsingEmployeeCode($employeeCode, $type);
            return $this->created($response);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return $this->noRecord(['message' => AttendanceController::INVALID_EMPLOYEE_ID]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    
    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }


}
