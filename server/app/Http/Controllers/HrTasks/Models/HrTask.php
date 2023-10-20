<?php
/**
 * HrTaskStatus
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\HrTasks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mews\Purifier\Casts\CleanHtml;

class HrTask extends  Model
{
    use SoftDeletes;

    protected $table = 'hr_tasks';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'description',
        'deadline',
        'hr_task_status_id',
        'created_by'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // protected $casts = [
    //     'description' => CleanHtml::class, // cleans both when getting and setting the value
    // ];

    /**
     * Get the Status record associated with the Task.
     */
    public function status()
    {
        return $this->belongsTo('App\Http\Controllers\HrTasks\Models\HrTaskStatus', 'hr_task_status_id', 'id');
    }


    /**
     * Get all the users assigned to this task.
     */
    public function assignedUsers (){
        return $this->belongsToMany('App\Models\UserPr', 'hr_assigned_tasks', 'hr_task_id', )->withTimestamps();
    }




}
