<?php

namespace App\Listeners;

use App\Events\EmployeeCodeForSignupEvent;
use App\Mail\EmployeeCodeForSignup;
use Illuminate\Support\Facades\Mail;

class EmployeeCodeForSignupListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(EmployeeCodeForSignupEvent $event)
    {
        Mail::to($event->email)->send(
            new EmployeeCodeForSignup($event->employee_number)
        );
 //
    }
}
