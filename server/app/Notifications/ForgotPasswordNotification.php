<?php
namespace App\Notifications;

use App\Mail\ForgotPasswordEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Http\Controllers\Users\Models\User;

class ForgotPasswordNotification extends Notification {

    use Queueable;
    private $user_ = null;
    private $token_ = null;

    public function __construct(User $user, string $token){
        $this->user_ = $user;
        $this->token_ = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($client){
        return (new ForgotPasswordEmail($this->user_, $this->token_))->subject('Forgot Password')->to($this->user_->email);
    }
}