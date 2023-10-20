<?php namespace App\Http\Controllers\Tickets\Enums;

use App\Helpers\Enum;
class TicketPriorityEnum extends Enum
    {
        const Low       = 0;
        const Medium    = 1;
        const High      = 2;
        const Highest   = 3;
        const Urgent    = 4;
    }
?>