<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Mail\VerifyAccountEmail;

class VerifyAccountNotification extends Notification {

    use Queueable;
    /** @var Loan */
        public $loan;
    /**
    * @param Loan $loan
    */
    public function __construct(array $NotificationData){
        $this->NotificationData_ = $NotificationData;
    }
    /**
    * Get the notification’s delivery channels.
    *
    * @param mixed $notifiable
    * @return array
    */
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($client){
        return (new VerifyAccountEmail($this->NotificationData_))->to('alee.waqas@outlook.com');
    }
}