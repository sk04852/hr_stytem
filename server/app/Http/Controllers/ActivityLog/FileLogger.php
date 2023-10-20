<?php
namespace App\Http\Controllers\ActivityLog;

use App\Http\Controllers\ActivityLog\Models\LogActivity;
use Log;
use App\Http\Controllers\ActivityLog\Interfaces\IActivityLogger;

class FileLogger implements IActivityLogger {

    private $activityLogger_;
    public function  __construct() {

    }

    public function addLog(array $log) {
        Log::debug($log);
    }
}
?>