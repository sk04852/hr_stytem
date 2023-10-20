<?php
namespace App\Http\Controllers\Log;
use App;
use App\Http\Controllers\ActivityLog\DatabaseLogger;

class LoggerBuilder
{
    private $logType_ = "database";
    public function getLogger() {
        switch($this->logType_) {
            case "database":
                $logger = App::make('App\Http\Controllers\ActivityLog\DatabaseLogger');
            break;
            case "file":
                $logger = App::make('App\Http\Controllers\ActivityLog\FileLogger');
            break;
        }
        return $logger;
    }

    public function getLogType() {
        return $this->logType_;
    }

    public function setLogType(string $logType) {
        $this->logType_ = $logType;
    }
}
?>