<?php

namespace App\Listeners;

use Exception;
use Illuminate\Support\Facades\Mail;
use App\Http\Services\EmailsService;
use App\Events\TicketNotifyUser;
use App\Mail\TicketNotifyUserEmail;
use DateTime;

class TicketNotifyUserLister
{
    private $emailsService_;
    public function __construct(EmailsService $emailsService)
    {
        $this->emailsService_ = $emailsService;
    }

    public function handle(TicketNotifyUser $event)
    {
        $mailAble = new TicketNotifyUserEmail($event->ticket, $event->user, $event->params);
        $email = $this->emailsService_->create('info@surprisy.com', 'Surprisy', $event->user->email, $mailAble->subject, $mailAble->render(), getModuleIdFromEntity($event->user), $event->user->id, 'Pending');
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
