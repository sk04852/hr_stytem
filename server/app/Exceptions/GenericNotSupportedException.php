<?php

namespace App\Exceptions;

use Exception;

class GenericNotSupportedException extends Exception
{
    private $message_;
 
    public function __construct($message)
    {
        $this->message_ = $message;
    }

    public function render(){
        return response()->json(['message' => $this->message_],200);
    }
}
