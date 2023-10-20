<?php

namespace App\Listeners;

use App\Events\ClientStatementEvent;
use App\Http\Services\EmailsService;
use App\Mail\ClientStatementEmail;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Mail;

class ClientStatementListener
{
    private $emailsService_;

    public function __construct(EmailsService $emailsService)
    {
        $this->emailsService_ = $emailsService;
    }

    public function handle(ClientStatementEvent $event)
    {
        
        $mailAble = new ClientStatementEmail($event->client, $event->transactions, $event->request);
        $email = $this->emailsService_->create('info@pos.com', 'POS', $event->client->email, $mailAble->subject, 'body', getModuleIdFromEntity($event->client), $event->client->id, 'Pending');
       
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
