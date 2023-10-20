<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeCodeForSignup extends Mailable
{
    use Queueable, SerializesModels;

   public $employee_number;
    public function __construct($employee_number)
    {
      $this->employee_number = $employee_number;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.employee-code-for-signup');
    }
}
