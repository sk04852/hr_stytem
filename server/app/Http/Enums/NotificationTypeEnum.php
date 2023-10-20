<?php namespace App\Http\Enums;

use App\Helpers\Enum;

class NotificationTypeEnum extends Enum
    {
        const SMS                   = 0;
        const PushNotification      = 1;
        const Internal              = 2;
        const Email                 = 3;
    }
?>
