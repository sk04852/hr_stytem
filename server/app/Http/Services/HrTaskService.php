<?php
/**
 * HrTaskService
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Services;

use App\Http\Controllers\HrTasks\HrTasksController;
use App\Http\Controllers\HrTasks\Models\HrTask;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class HrTaskService
{

    public function getMention($search)
    {
        $users = DB::table('users')
            ->join('user_pr', 'users.id', '=', 'user_pr.user_ID')
            ->whereNull('users.deleted_at')
            ->where('users.name', 'Like', "%$search%")
            ->select('user_pr.id as id', 'users.name as name', DB::raw("'users' as type"));


        $job = DB::table('jobs_pr')
            ->join('jobs', 'jobs_pr.id', '=', 'jobs.job_pr_id')
            ->where('jobs.offer_name', 'Like', "%$search%")
            ->whereNull('jobs_pr.deleted_at')
            ->select('jobs_pr.id as id', 'jobs.offer_name as name', DB::raw("'job' as type"));

        $result = DB::table('companies')
            ->whereNull('companies.deleted_at')
            ->where('companies.name', 'Like', "%$search%")
            ->union($users)
            ->union($job)
            ->select('companies.company_pr_id as id', 'companies.name as name', DB::raw("'company' as type"))
            ->get();

        return $result;
    }

    public function getAllInCompleteTask(){
        return HrTask::where('hr_task_status_id', HrTasksController::IN_PROGRESS_STATUS)
            ->where('notify_incomplete_status', '=', 0)
            ->where('deadline', '<', Carbon::today()->format('Y-m-d'))
            ->get();
    }

}
