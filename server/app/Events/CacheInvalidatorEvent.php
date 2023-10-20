<?php

namespace App\Events;

use App\Models\BaseModel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CacheInvalidatorEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $model;
    public function __construct(BaseModel $model)
    {
        $this->model = $model;
    }
}
