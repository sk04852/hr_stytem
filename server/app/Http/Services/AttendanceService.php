<?php
namespace App\Http\Services;

use Exception;
use Carbon\Carbon;
use App\Http\Controllers\EmsAttendance\Models\EmployeeAttendace;
use App\Http\Controllers\EmsEmployee\Models\Employee;

class AttendanceService {
    private $model_;
    private $employeeModel_;
    private $employeesService_;
    
    public function __construct(Employee $employeeModel, 
                                EmployeeAttendace $model,
                                EmployeesService $employeesService) {
        $this->model_ = $model;
        $this->employeeModel_ = $employeeModel;
        $this->employeesService_ = $employeesService;
    }

    public function getEmployeeAttendanceStatement(int $employeeId, string $type = "Weekly") {

        $now = Carbon::now();
        $type = strtolower($type);
        $trackedTimesInMins = 0;
        if($type == "weekly") {
            $startDate = $now->startOfWeek()->format('Y-m-d');
            $endDate = $now->endOfWeek()->format('Y-m-d');
        } else if ($type == "monthly") {
            $startDate = $now->startOfMonth()->format('Y-m-d');
            $endDate = $now->endOfMonth()->format('Y-m-d');
        }

        $employee = $this->employeeModel_->where('id', $employeeId)->first();
        $trackedTimings = [];
        $trackedTimings = $employee
                     ->attendance()
                     ->selectRaw('TIMESTAMPDIFF(MINUTE, start_time, end_time) as mins_tracked, date')
                     ->where('date', '>=', $startDate)
                     ->where('date', '<=', $endDate)
                     ->get();
        foreach($trackedTimings as $trackedTime) {
            $trackedTimesInMins += $trackedTime->mins_tracked;
        }

        $workSchedule = $this->employeesService_->getEmployeeWorkSchedule($employee);
        
        if(!$employee) {
            throw new Exception("Employee not found");
        }
        $workableHoursPerWeek = (float)$workSchedule->param1;
        $workableTimePerWeekInMins = $workableHoursPerWeek * 60;
        $workableTime = [
            'hours'=> floor($workableTimePerWeekInMins/60),
            'mins'=> $workableTimePerWeekInMins %60
        ];

        $trackedTime = [
            'hours'=> floor($trackedTimesInMins / 60),
            'mins'=> $trackedTimesInMins % 60
        ];

        $balanceTime = [
            'hours'=> floor(($workableTimePerWeekInMins -$trackedTimesInMins) / 60),
            'mins'=> ($workableTimePerWeekInMins - $trackedTimesInMins) % 60
        ];

        $overTime = [
            'hours'=> floor(($trackedTimesInMins - $workableTimePerWeekInMins) / 60),
            'mins'=> ($trackedTimesInMins - $workableTimePerWeekInMins) % 60
        ];

        return ['from'=> $startDate, 
                'until'=> $endDate, 
                'tracked_timings'=> $trackedTimings, 
                'total_work_time'=> $workableTime,
                'tracked'=> $trackedTime,
                'balance'=> $balanceTime,
                'over_time'=> $overTime,
                'work_schedule'=> $workSchedule];
    }

    public function markAttendanceUsingEmployeeCode(int $employeeCode, string $type) {
        $message = null;
        $attendance = null;
        $employee = $this->employeeModel_->where('employee_number', $employeeCode)->first();
        if(!$employee) {
            throw new Exception('Invalid code, employee not recognized');
        }

        $now = new Carbon();
        if (strtolower($type) === 'in') {
            $alreadyClockedIn = $this->model_->where('employee_id', $employee->id)->where('end_time', NULL)->orderBy('date', 'DESC')->first();
            if ($alreadyClockedIn) {
                throw new Exception('You are already clocked-in');
            }
            $attendance = [
                    "employee_id"=> $employee->id,
                    "date" => $now->format('Y-m-d'),
                    "entry_type" => "Attendance",
                    'source'=> 'Code',
                    "start_time" => $now->format('H:i:s'),
            ];
            $attendance = $this->model_->fill($attendance);
            if ( $attendance->save()) {
                $message = "You have clocked in successfully";
            }
        } else {
            $attendance = $this->model_->where('employee_id', $employee->id)->where('end_time', NULL)->orderBy('date', 'DESC')->first();
            if (!$attendance) {
                throw new Exception('You are not clocked-in, please clock in first');
            } 
            $attendance->end_time = $now->format('H:i:s');
            if ($attendance->save()) {
                $message = 'Attendance marked successfully';
            }
        }
        return ['message'=> $message, 'attendance'=> $attendance];
    }

    public function isAttendenceAlreadyMarked(int $employeeId, string $day, string $entry_type,  string $start_time, string $end_time = null) {
        $alreadyMarked = NULL;
        if($end_time == null) {
            $alreadyMarked = $this->model_
            ->whereRaw('employee_id = ? AND date = ? AND start_time = ? AND entry_type = ?', [$employeeId, $day,$start_time, $entry_type])
            ->first();
        } else {
            $alreadyMarked = $this->model_
            ->whereRaw('employee_id = ? AND date = ? AND (? between start_time AND end_time) AND (? between start_time AND end_time) AND entry_type = ?', [$employeeId, $day,$start_time, $end_time, $entry_type])
            ->first();
        }
        return $alreadyMarked;
    }
}

?>