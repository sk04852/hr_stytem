<?php

namespace App\Http\Controllers\Workflows\Models;

use App\Models\BaseModel;

class WorkflowEmail extends BaseModel
{
	protected $table = 'workflow_emails';
	public static $snakeAttributes = false;

	protected $fillable = [
        'relation_id',
        'module_id',
        'subject',
        'from',
        'from_name',
        'body',
        'to',
        'cc',
        'bcc',
        'body_var'
    ];

}
