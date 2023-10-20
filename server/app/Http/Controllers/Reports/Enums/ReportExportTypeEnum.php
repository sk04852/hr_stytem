<?php namespace App\Http\Controllers\Reports\Enums;
use App\Helpers\Enum;

class ReportExportTypeEnum extends Enum {
        const Pdf             = 1;
        const Excel           = 2;
        const Csv             = 3;
        const Json            = 4;
}
?>