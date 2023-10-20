<?php

namespace App\Listeners;

use App\Events\TwoFactorAuthEnabledEvent;
use App\Mail\TwoFactorAuthEnabledEmail;
use Illuminate\Support\Facades\Mail;

class TwoFactorAuthEnabledListener
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
    public function handle(TwoFactorAuthEnabledEvent $event)
    {
        Mail::to($event->user->email)->send(
            new TwoFactorAuthEnabledEmail($event->user , $event->code , $event->enableEmail, $event->verifyOTP)
        );
    }
}
