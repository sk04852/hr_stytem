<?php
namespace App\Listeners;

use App\Events\NewClientAddedEvent;
use Illuminate\Support\Facades\Mail;
use App\Http\Services\EmailsService;
use App\Mail\ClientSignUpEmail;
use DateTime;
use Exception;

class NewClientAddedListener
{
    private $emailsService_;

    public function __construct(EmailsService $emailsService)
    {
        $this->emailsService_ = $emailsService;
    }

    public function handle(NewClientAddedEvent $event)
    {
        
        $mailAble = new ClientSignUpEmail($event->client, $event->code);
        $email = $this->emailsService_->create('info@pos.com', 'POS', $event->client->email, $mailAble->subject, $mailAble->render(), getModuleIdFromEntity($event->client), $event->client->id, 'Pending');
       
        try {
            if(!empty($event->client->email)) {
                Mail::to($event->client->email)->send($mailAble);
            }
            $email->delivery_status = 'Delivered';
            $email->delivery_time = new DateTime();
        } catch(Exception $ex) {
            $email->delivery_status = 'Failed';
        }
           $email->save();
    }
}
