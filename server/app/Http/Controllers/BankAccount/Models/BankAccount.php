<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Controllers\BankAccount\Models;

use App\Models\BaseModel;

class BankAccount extends BaseModel
{
    protected $table = 'bank_accounts';
    public static $snakeAttributes = false;

    protected $fillable = [
        'bank_id',
        'account_number',
        'iban',
        'branch_code',
        'swift_code',
        'bic',
        'currency',
    ];
}
