<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateIncomingPaymentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $clientInformation;
    public $userInformation;
    public $paymentInformation;
    public function __construct($userInformation, $clientInformation, $paymentInformation)
    {
        $this->userInformation = $userInformation;
        $this->clientInformation = $clientInformation;
        $this->paymentInformation = $paymentInformation;
    }
}
