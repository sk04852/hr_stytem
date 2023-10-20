<?php namespace App\Http\Controllers\Modules\Enums;

use App\Helpers\Enum;

class SaleTransactionTypeEnum extends Enum
    {
        const CashSale        = 1;
        const CreditSale      = 2;
        const ClientPayment   = 3;
        const EmployeeReceivable   = 4;
    }
?>
