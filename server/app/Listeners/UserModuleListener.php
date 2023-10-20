<?php

namespace App\Listeners;

use App\Events\User\UserCreated;
use App\Http\Controllers\Mail\Services\MailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserModuleListener
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

    public function handleUserCreated ($event){
        $mail_service = new MailService();
        $to = $event->user_pr->email;
        $from = env('MAIL_FROM_ADDRESS');
        $subject = 'Uus kasutaja Loodud veebilehel '. env('WEBSITE_URL');
        $body = "Uus kasutaja veebilehel " . env('WEBSITE_URL') . " edukalt loodud. <br>
                Kasutajanimi: ". $to ." <br>
                Parool: ". $event->raw_password ;
        $mail_service->sendMail($to, $from, $subject, $body);
    }


    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function subscribe($events){
        $events->listen(
            UserCreated::class,
            'App\Listeners\UserModuleListener@handleUserCreated'
        );
    }
}
