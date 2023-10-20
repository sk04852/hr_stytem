<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;

class NewUserRegistered extends Notification {

    use Queueable;
    /** @var Loan */
        public $loan;
    /**
    * @param Loan $loan
    */
    public function __construct(){

    }
    /**
    * Get the notification’s delivery channels.
    *
    * @param mixed $notifiable
    * @return array
    */
    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($client){
        return (new SlackMessage)->content("Client: ".$client->id." A new user ".$client->getFullName()."(".$client->getEmail().") has registered.");
    }
}