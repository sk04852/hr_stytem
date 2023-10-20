<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeCodeForSignupEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

   public $email;
   public $employee_number;

    public function __construct( $email, $employee_number)
    {
        $this->email=$email;
        $this->employee_number=$employee_number;
    }

   
}
