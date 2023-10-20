<?php
namespace App\Http\Services;

use App\Http\Controllers\EmsEmployee\Models\Employee;
use App\Http\Controllers\TimeOff\Models\EmployeeTimeOff;
use App\Http\Controllers\TimeOff\Models\TimeoffType;
use Carbon\Carbon;
use DateTime;
use Exception;

class EmployeeTimeOffService {
    
    private $employeesService_;
    private $attendanceService_;
    private $employeeTimeOffModel_;
    private $timeOffTypeModle_;
    private $defaultPolicyId_ = 1;
    public function __construct(
        EmployeesService $employeesService,
        AttendanceService $attendanceService,
        EmployeeTimeOff $employeeTimeOffModel,
        TimeoffType $timeOffTypeModel) {
            $this->employeesService_ = $employeesService;
            $this->attendanceService_ = $attendanceService;
            $this->employeeTimeOffModel_ = $employeeTimeOffModel;
            $this->timeOffTypeModel_ = $timeOffTypeModel;
    }

    public function canEmployeeRequestTimeOff(Employee $employee, TimeoffType $timeoffType, DateTime $startDate, DateTime $endDate) {
        
        if(!$timeoffType) {
            throw new Exception('Invalid timeoff type');
        }

        $absentDaysRequested = (float)$endDate->diff($startDate)->format('%a');
        $allocationMethod = $timeoffType->allocation_method;
        $allowedMethods = ['Accrual','Unlimited'];
        if(!in_array($timeoffType->allocation_method, $allowedMethods)) {
            throw new Exception('Invalid timeoff type allocation method');
        }

        if($allocationMethod === 'Unlimited') {
            return true;
        } else if($allocationMethod === 'Accrual') {
            $accrualStartEndTime = $this->getAccrualStartEndTime($timeoffType);
            $previousTimeOffs = $this->getPastTimeOffs($employee->id, $timeoffType->id, $accrualStartEndTime[0], $accrualStartEndTime[1]);
            $numberOfAllowedDays = $timeoffType->accrual_allowance;
            $numberOfConsumedDays = $previousTimeOffs->sum('absent_days');

            if($numberOfAllowedDays >= ($numberOfConsumedDays + $absentDaysRequested)) {
                return true;
            }
        }
        return false;
    }

    public function getAccrualStartEndTime(TimeoffType $timeoffType) {
        $now = new Carbon();
        $accrualPeriodStart = $now->copy()->startOfYear();
        $accrualPeriodEnd = $now->copy()->startOfMonth();
        $accrualPeriod = $timeoffType->accrual_period;

        if($accrualPeriod === 'Jan-Dec') {
            $accrualPeriodStart = $now->copy()->startOfYear();
            $accrualPeriodEnd = $now->copy()->endOfYear();
        } else if($accrualPeriod === 'Jun-May') {
            $accrualPeriodStart = $now->copy()->startOfYear()->addMonth(5)->startOfMonth();
            $accrualPeriodEnd = $now->copy()->startOfYear()->addYear(1)->addMonth(4)->endOfMonth();
        } else if($accrualPeriod === 'Custom') {
            $accrualPeriodStart = new Carbon($timeoffType->accrual_start_date);
            $accrualPeriodEnd = new Carbon($timeoffType->accrual_end_date);
        }

        return [$accrualPeriodStart, $accrualPeriodEnd];
    }

    public function getPastTimeOffs(int $employeeId, int $timeOffTypeId, Carbon $from, Carbon $to) {
        return $this->employeeTimeOffModel_->where(function($query) use($employeeId, $timeOffTypeId, $from, $to) {
            $query->where('timeoff_type_id', $timeOffTypeId)
                  ->where('employee_id', $employeeId)
                  ->where('status', 'Approved')
                  ->where('from_date', '>=', $from)->where('to_date', '<=', $to);
        })->get();
    }

    public function getTimeOff(int $timeOffId) {
        return $this->employeeTimeOffModel_->where('id', $timeOffId)->with('type')->first();
    }

    public function getEmployeeTimeoffRequests(int $employeeId) {
        return $this->employeeTimeOffModel_->where('employee_id', $employeeId)->with(['type','employee'])->get();
    }

    public function getTimeoffRequests($cond = []) {
        return $this->employeeTimeOffModel_->where($cond)->with(['type','employee'])->get();
    }

    public function getEmployeeTimeOffBalances(Employee $employee) {
        $timeOffTypes = $this->timeOffTypeModel_->select(['id', 'name', 'allocation_method', 'accrual_allowance'])->where('policy_id', $this->defaultPolicyId_)->get();
        $timeOffTypes->map(function($timeOffType) use($employee) { 
                $accrualStartEndTime = $this->getAccrualStartEndTime($timeOffType);
                $timeOffs = $this->getPastTimeOffs($employee->id, $timeOffType->id, $accrualStartEndTime[0], $accrualStartEndTime[1]);
                $timeOffType->used_period = $timeOffs->sum('absent_days');
                
                if($timeOffType->allocation_method === 'Unlimited') {
                    $timeOffType->available_period = 'Unlimited';
                    $timeOffType->percentage_used = 0;
                } else {
                    $periodAvailable = $timeOffType->accrual_allowance - $timeOffType->used_period;
                    $timeOffType->available_period = $periodAvailable;
                    $timeOffType->percentage_used = (100 / $timeOffType->accrual_allowance) * $timeOffType->used_period;

                }
        });

        return $timeOffTypes;
    }
}

?>