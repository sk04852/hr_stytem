<?php

namespace App\Mail;

use App\Http\Controllers\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $data = [];
    public $subject = 'Forgot Password Email';
    public function __construct(User $user, string $token)
    {
        $this->data = [
            'full_name'=> $user->full_name,
            'token'=> $token
        ];
    }

    public function build()
    {
        return $this->view('emails.forgot-password-email')->with($this->data);
    }
}
