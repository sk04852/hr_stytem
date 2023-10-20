<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterationWithPromoCodeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = 'PromoCode Email';

    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.registeration-with-promo-code');
    }
}
