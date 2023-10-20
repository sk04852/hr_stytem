<?php namespace App\Http\Enums;

use App\Helpers\Enum;

class NotificationDeliveryStatus extends Enum
    {
        const Pending    = 0;
        const Picked     = 1;
        const Delivered    = 2;
    }
?>
