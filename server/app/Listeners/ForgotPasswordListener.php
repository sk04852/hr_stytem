<?php
namespace App\Listeners;

use App\Events\ForgotPasswordEvent;
use Illuminate\Support\Facades\Mail;
use App\Http\Services\EmailsService;
use App\Mail\ForgotPasswordClientEmail;
use App\Mail\ForgotPasswordEmail;
use DateTime;
use Exception;

class ForgotPasswordListener
{
    private $emailsService_;

    public function __construct(EmailsService $emailsService)
    {
        $this->emailsService_ = $emailsService;
    }

    public function handle(ForgotPasswordEvent $event)
    {

        Mail::to($event->user->email)->send(
            new ForgotPasswordEmail($event->user, $event->token)
        );

        // $mailAble = new ForgotPasswordEmail($event->user, $event->token);
        // $email = $this->emailsService_->create('info@heilo.com', 'Heilo', $event->user->email, $mailAble->subject, $mailAble->render(), getModuleIdFromEntity($event->user), $event->user->id, 'Pending');

        // try {
        //     if(!empty($event->user->email)) {
        //         Mail::to($event->user->email)->send($mailAble);
        //     }
        //     $email->delivery_status = 'Delivered';
        //     $email->delivery_time = new DateTime();
        // } catch(Exception $ex) {
        //     $email->delivery_status = 'Failed';
        // }
        //    $email->save();
    }
}
