<?php

namespace App\Http\Controllers\Workflows\Models;

use App\Models\BaseModel;


class WorkflowInvokeFunction extends BaseModel
{
	protected $table = 'workflow_invoke_functions';
	public static $snakeAttributes = false;

	protected $fillable = [
		'class_name',
		'function_name',
		'function_parameters',
	];

}
