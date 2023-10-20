<?php
namespace App\Http\Controllers\ActivityLog;

use App\Http\Controllers\ActivityLog\Models\LogActivity;
use App\Http\Controllers\ActivityLog\Interfaces\IActivityLogger;

class DatabaseLogger implements IActivityLogger {

    private $activityLogger_;
    public function  __construct(LogActivity $activityLogger) {
        $this->activityLogger_ = $activityLogger;
    }

    public function addLog(array $log) {
        $this->activityLogger_->fill($log);
        $this->activityLogger_->save();
    }
}
?>