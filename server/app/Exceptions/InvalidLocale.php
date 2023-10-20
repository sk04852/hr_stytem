<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class InvalidLocale extends Exception
{
    private $message_;

    public function __construct($message)
    {
        $this->message_ = $message;
    }

    public function render(){
        return response()->json(['message' => $this->message_],Response::HTTP_BAD_REQUEST);
    }
}
