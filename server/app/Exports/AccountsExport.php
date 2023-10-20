<?php

namespace App\Exports;

use App\Http\Controllers\Accounts\Models\Account;
use Maatwebsite\Excel\Concerns\FromCollection;

class AccountsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Account::all();
    }
}
