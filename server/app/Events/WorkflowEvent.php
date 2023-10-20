<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkflowEvent
{
    public $moduleId;
    public $model;
    public $crudOption;

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct($moduleId, $model, $crudOption)
    {
        $this->moduleId = $moduleId;
        $this->model = $model;
        $this->crudOption = $crudOption; 
    }
}
