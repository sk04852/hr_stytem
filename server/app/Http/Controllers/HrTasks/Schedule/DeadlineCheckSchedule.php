<?php
/**
 * DeadlineCheckSchedule
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\HrTasks\Schedule;



use App\Http\Services\HrTaskService;
use App\Models\UserPr;
use App\Notifications\HrTaskIncomplete;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class DeadlineCheckSchedule
{

    public function __invoke()
    {
        $hr_task_service = new HrTaskService();
        $in_complete_tasks = $hr_task_service->getAllInCompleteTask();
        if($in_complete_tasks->isNotEmpty()){
            foreach ($in_complete_tasks as $in_complete_task){
                $task_creator = UserPr::where('id', $in_complete_task->created_by)->first();
                Notification::send($task_creator, new HrTaskIncomplete([
                    'title' => 'Ülesanne on lõpetamata: '. $in_complete_task->description,
                    'task_id' => $in_complete_task->id,
                    'description' => $in_complete_task->description,
                    'deadline' => $in_complete_task->deadline,
                    'image' => '',
                    'notification_time' => Carbon::now()->toDateTimeString()
                ]));
                $in_complete_task->notify_incomplete_status = 1;
                $in_complete_task->save();
            }
        }
    }

}
