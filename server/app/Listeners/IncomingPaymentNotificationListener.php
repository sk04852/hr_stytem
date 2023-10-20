<?php

namespace App\Listeners;

use DateTime;
use Exception;
use App\Http\Services\EmailsService;
use App\Mail\ThankYouForPaymentMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\IncomingPaymentNotificationMail;

class IncomingPaymentNotificationListener
{
    private $emailsService_;

    public function __construct(EmailsService $emailsService)
    { 
        $this->emailsService_ = $emailsService;
    }
    public function handle($event)
    {
        $companyEmail = $event->userInformation->company->primary_email;
        $outletEmail =  $event->userInformation->company->outlets[0]->email;
        $paymentMail = new IncomingPaymentNotificationMail($event->userInformation, $event->clientInformation, $event->paymentInformation);
     
        $email = $this->emailsService_->create('info@pos.com', 'POS', $companyEmail, $paymentMail->subject, 'body', getModuleIdFromEntity($event->userInformation->company), $event->clientInformation->id);
        try {
                Mail::to($companyEmail)->send($paymentMail); 
                $email->delivery_status = 'Delivered';
                $email->delivery_time = new DateTime();
        } catch(Exception $ex) {
            $email->delivery_status = 'Failed';
        }
        $email->save();

        if(!empty($event->clientInformation->email)) {
            try {
                    $thankYouMail = new ThankYouForPaymentMail($event->paymentInformation);
                    $email = $this->emailsService_->create('info@pos.com', 'POS', $event->clientInformation->email, $thankYouMail->subject, 'body', getModuleIdFromEntity($event->clientInformation), $event->clientInformation->id);
                    Mail::to($event->clientInformation->email)->send($thankYouMail); 
                    $email->delivery_status = 'Delivered';
                    $email->delivery_time = new DateTime();
            } catch(Exception $ex) {
                $email->delivery_status = 'Failed';
            }
               $email->save();
        }

       
        if (!empty($outletEmail)) {
            $outletMail = new IncomingPaymentNotificationMail($event->userInformation, $event->clientInformation, $event->paymentInformation);
     
            $email = $this->emailsService_->create('info@pos.com', 'POS', $outletEmail, $outletMail->subject, 'body', getModuleIdFromEntity($event->userInformation->company->outlets[0]) , $event->clientInformation->id);
            try {
                    Mail::to($outletEmail)->send($outletMail);
                    $email->delivery_status = 'Delivered';
                    $email->delivery_time = new DateTime();
            } catch(Exception $ex) {
                $email->delivery_status = 'Failed';
            }
            $email->save();
        }
    }
}
