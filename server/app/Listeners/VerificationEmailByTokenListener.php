<?php

namespace App\Listeners;

use App\Events\VerificationEmailByTokenEvent;
use App\Mail\VerificationEmailByToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class VerificationEmailByTokenListener
{
  
    public function __construct()
    {
        //
    }


    public function handle(VerificationEmailByTokenEvent $event)
    {
        Mail::to($event->email)->send(
            new VerificationEmailByToken($event->first_name,)
        );

    }
}
