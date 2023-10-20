<?php

namespace App\Listeners;

use App\Events\SendEmailEvent;
use App\Http\Services\EmailsService;
use App\Mail\GenericEmail;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendEmailEventListener
{

    public $emailsService_;
    public function __construct(EmailsService $emailsService)
    {
        $this->emailsService_ = $emailsService;
    }

    public function handle(SendEmailEvent $event)
    {
        $mailAble = new GenericEmail($event->email);
        $mailAble->subject($event->email->subject);
        $mailAble = $mailAble->build();
        $email = $this->emailsService_->create($mailAble->from[0]['address'], $mailAble->from[0]['name'], $event->email->to, $event->email->subject, $mailAble->render(), $event->email->module_id, $event->email->relation_id, 'Pending');
        try {
            if(!empty($event->email->to)) {
                Mail::to($event->email->to)->send($mailAble);
            }
            $email->delivery_status = 'Delivered';
            $email->delivery_time = new DateTime();
        } catch(Exception $ex) {
            $email->delivery_status = 'Failed';
        }
        $email->save();
    }
}
