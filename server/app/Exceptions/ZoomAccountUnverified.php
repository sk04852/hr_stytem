<?php

namespace App\Exceptions;

use Exception;

class ZoomAccountUnverified extends Exception
{
    private $message_;
 
    public function __construct($message)
    {
        $this->message = $message;
    }
}
