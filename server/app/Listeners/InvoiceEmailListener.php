<?php

namespace App\Listeners;

use App\Events\InvoiceEmailEvent;
use App\Http\Services\EmailsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\InvoiceEmail;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Mail;

class InvoiceEmailListener implements ShouldQueue
{
    private $emailsService_;

    public function __construct(EmailsService $emailsService)
    {
        $this->emailsService_ = $emailsService;
    }

    public function handle(InvoiceEmailEvent $event)
    {
        if(is_null($event->client->email)){
            throw new Exception('Client email not found.');
        }
        $mailAble = new InvoiceEmail($event->client, $event->invoice, $event->products);
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
