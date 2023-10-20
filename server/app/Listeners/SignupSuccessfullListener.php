<?php

namespace App\Listeners;

use App\Events\SignupSuccessfullEvent;
use App\Mail\SignupSuccessfull;
use Illuminate\Support\Facades\Mail;

class SignupSuccessfullListener
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
    public function handle(SignupSuccessfullEvent $event)
    {
        Mail::to($event->email)->send(
            new SignupSuccessfull($event->first_name, $event->email_verification_token)
        );
    }
}
