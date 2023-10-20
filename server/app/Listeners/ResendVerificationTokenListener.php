<?php

namespace App\Listeners;

use App\Events\ResendVerificationTokenEvent;
use App\Mail\ResendVerificationToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ResendVerificationTokenListener
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

    public function handle(ResendVerificationTokenEvent $event)
    {
        Mail::to($event->email)->send(
            new ResendVerificationToken($event->email_verification_token)
        );

    }
}
