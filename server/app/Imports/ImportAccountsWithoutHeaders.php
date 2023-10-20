<?php

namespace App\Imports;

use App\Http\Controllers\Accounts\Models\Account;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportAccountsWithoutHeaders implements ToModel
{
   /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
            //
    }
}
