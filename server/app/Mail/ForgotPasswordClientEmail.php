<?php

namespace App\Mail;

use App\Http\Controllers\Clients\Models\Client;
use App\Http\Controllers\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordClientEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $code;

    public $subject = 'Forgot Password Email';

    public function __construct(User $user, $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    public function build()
    {
        return $this->markdown('emails.forgot-password-email');
    }
}
