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

class HrTaskStatus extends  Model
{
    use SoftDeletes;

    const IN_PROGRESS_STATUS = 1;
    const COMPLETED_STATUS = 2;

    protected $table = 'hr_task_statuses';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the User Pr record associated with the language.
     */
    public function tasks()
    {
        return $this->hasMany('App\Http\Controllers\HrTasks\Models\HrTask', 'created_by', 'id');
    }

}
