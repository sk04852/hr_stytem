<?php

namespace App\Listeners;

use DateTime;
use Exception;
use App\Http\Services\EmailsService;
use App\Mail\IncomingPaymentNotificationMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\ThankYouForPaymentMail;


class UpdateIncomingPaymentEventListener
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
        $newPaymentTransactionEmail = new IncomingPaymentNotificationMail($event->userInformation, $event->clientInformation, $event->paymentInformation);
        $newPaymentTransactionEmail = $newPaymentTransactionEmail->build();
        $email = $this->emailsService_->create($newPaymentTransactionEmail->from[0]['address'], $newPaymentTransactionEmail->from[0]['name'], $companyEmail, $newPaymentTransactionEmail->subject, 'body', getModuleIdFromEntity($event->userInformation->company), $event->clientInformation->id);
        try {
                Mail::to($companyEmail)->send($newPaymentTransactionEmail);
                $email->delivery_status = 'Delivered';
                $email->delivery_time = new DateTime();
        } catch(Exception $ex) {
            $email->delivery_status = 'Failed';
        }

        $email->save();

        if(!empty($event->clientInformation->email)) {
            try {
                    $thankYouMail = new ThankYouForPaymentMail($event->paymentInformation);
                    $thankYouMail = $thankYouMail->build();
                    $email = $this->emailsService_->create($thankYouMail->from[0]['address'], $thankYouMail->from[0]['name'], $event->clientInformation->email, $thankYouMail->subject, 'body', getModuleIdFromEntity($event->clientInformation), $event->clientInformation->id);
                    Mail::to($event->clientInformation->email)->send($thankYouMail);
                    $email->delivery_status = 'Delivered';
                    $email->delivery_time = new DateTime();
            } catch(Exception $ex) {
                $email->delivery_status = 'Failed';
            }
               $email->save();
        }


        if (!empty($outletEmail)) {
            $newPaymentTransactionEmail = new IncomingPaymentNotificationMail($event->userInformation, $event->clientInformation, $event->paymentInformation);
            $newPaymentTransactionEmail = $newPaymentTransactionEmail->build();
            $email = $this->emailsService_->create($thankYouMail->from[0]['address'], $newPaymentTransactionEmail->from[0]['name'], $outletEmail, $newPaymentTransactionEmail->subject, 'body', getModuleIdFromEntity($event->userInformation->company->outlets[0]) , $event->clientInformation->id);
            try {
                    Mail::to($outletEmail)->send($newPaymentTransactionEmail);
                    $email->delivery_status = 'Delivered';
                    $email->delivery_time = new DateTime();
            } catch(Exception $ex) {
                $email->delivery_status = 'Failed';
            }
            $email->save();
        }
    }
}
