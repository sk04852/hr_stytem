<?php namespace App\Http\Enums;

use App\Helpers\Enum;

class NotificationReadStatus extends Enum
    {
        const Unread    = 0;
        const Read      = 1;
        const All       = 2;
    }
?>
