<?php namespace App\Http\Controllers\Tickets\Enums;

use App\Helpers\Enum;
class TicketStatusEnum extends Enum
    {
        const Open          = 0;
        const InProgress    = 1;
        const Done          = 2;
        const Closed        = 3;
        const Cancelled     = 4;
    }
?>