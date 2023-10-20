<?php

namespace App\Http\Controllers\Workflows\Models;

use App\Models\BaseModel;


class WorkflowTodo extends BaseModel
{
	protected $table = 'workflow_todos';
	public static $snakeAttributes = false;

	protected $fillable = [
		'workflow_id',
		'todo',
		'task_title',
		'module_id',
		'data'
	];

}
