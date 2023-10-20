<?php
namespace App\Http\Controllers\ActivityLog\Interfaces;

interface IActivityLogger {
    public function addLog(array $log);
}
?>