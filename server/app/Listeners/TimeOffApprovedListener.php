<?php

namespace App\Listeners;

use App\Events\TimeOffApprovedEvent;
use App\Mail\TimeOffApprovedEmail;

use Illuminate\Support\Facades\Mail;

class TimeOffApprovedListener
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
    public function handle(TimeOffApprovedEvent $event)
    {

        Mail::to($event->user->email)->send(
            new TimeOffApprovedEmail($event->timeOffRequest, $event->user, $event->status, $event->note)
        );
    }
}
