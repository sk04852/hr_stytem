<?php

namespace App\Http\Controllers\Modules\Enums;

use App\Helpers\Enum;

class LogAction extends Enum
{
    const Created      = 'Created';
    const Updated      = 'Updated';
    const Deleted      = 'Deleted';
}
