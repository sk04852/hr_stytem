<?php

namespace App\Listeners;

use App\Events\UserSignedUpEvent;
use App\Http\Services\EmailsService;
use App\Mail\UserSignedUpEmail;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Mail;

class UserSignedUpEventListener
{
    public $emailsService_;
    public function __construct(EmailsService $emailsService)
    {
        $this->emailsService_ = $emailsService;
    }

    public function handle(UserSignedUpEvent $event)
    {
        $mailAble = new UserSignedUpEmail($event->user);
        $mailAble = $mailAble->build();
        $email = $this->emailsService_->create($mailAble->from[0]['address'], $mailAble->from[0]['name'], $event->user->email, $mailAble->subject, $mailAble->render(), getModuleIdFromEntity($event->user), $event->user->id, 'Pending');
        try {
            if(!empty($event->user->email)) {
                Mail::to($event->user->email)->send($mailAble);
            }
            $email->delivery_status = 'Delivered';
            $email->delivery_time = new DateTime();
        } catch(Exception $ex) {
            $email->delivery_status = 'Failed';
        }
        $email->save();
    }
}
