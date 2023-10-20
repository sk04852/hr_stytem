<?php

namespace App\Listeners;

use App\Events\TwoFactorAuthDisabledEvent;
use App\Mail\TwoFactorAuthDisabledEmail;
use Illuminate\Support\Facades\Mail;

class TwoFactorAuthDisabledListener
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
    public function handle(TwoFactorAuthDisabledEvent $event)
    {
        Mail::to($event->user->email)->send(
            new TwoFactorAuthDisabledEmail($event->user,$event->isDisableEmailRequest)
        );
    }
}
