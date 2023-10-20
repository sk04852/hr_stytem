<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncomingPaymentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $clientInformation;
    public $userInformation;
    public $paymentInformation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userInformation, $clientInformation, $paymentInformation)
    { 
        $this->userInformation = $userInformation;
        $this->clientInformation = $clientInformation;
        $this->paymentInformation = $paymentInformation;
    }
}
