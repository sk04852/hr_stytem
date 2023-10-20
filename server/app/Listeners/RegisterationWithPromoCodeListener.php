<?php

namespace App\Listeners;

use App\Events\RegisterationWithPromoCode;
use App\Http\Services\EmailsService;
use App\Mail\RegisterationWithPromoCodeEmail;
use DateTime;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class RegisterationWithPromoCodeListener
{
    private $emailsService_;

    public function __construct(EmailsService $emailsService)
    {
        $this->emailsService_ = $emailsService;
    }

    public function handle(RegisterationWithPromoCode $event)
    {

        $mailAble = new RegisterationWithPromoCodeEmail($event->user);
        $email = $this->emailsService_->create('info@crm.com', 'CRM', $event->user->user->email, $mailAble->subject, $mailAble->render(), getModuleIdFromEntity($event->user->user), $event->user->user->id, 'Pending');

        try {
            if (!empty($event->user->user->email)) {
                Mail::to($event->user->user->email)->send($mailAble);
            }
            $email->delivery_status = 'Delivered';
            $email->delivery_time = new DateTime();
        } catch (Exception $ex) {
            $email->delivery_status = 'Failed';
        }
        $email->save();
    }
}
