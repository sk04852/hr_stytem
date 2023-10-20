<?php

namespace App\Http\Controllers\Accounts;

use App\Exports\AccountsExport;
use App\Http\Controllers\Accounts\Models\Account;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Accounts\Requests\FileRequest;
use App\Imports\ImportAccounts;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class BulkUploadAccounts extends Controller
{
    public function __construct() {

    }

    public function message($message){
        return $this->created(['message' => $message]);
    }

    //WORK IN PROGRESS
    public function importAccounts(FileRequest $request){
        try {
            if ($request->has_headers == 0) {
                // Excel::import(new ImportAccountsWithoutHeaders,request()->file('csv_file'));
                // return response()->json(['message' => 'Accounts Imported Successfully']);
            }
            $file = $request->file('csv_file');
            $import = new ImportAccounts();
            $import->import($file);
            $accountsValidationErrors = $import->getErrors();
            if (count($accountsValidationErrors)) {
                return response()->json(['errors' => $accountsValidationErrors], 422);
            }
            return response()->json(['message' => 'Data Imported Successfully']);

       } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function exportAccounts()
    {
        return Excel::download(new AccountsExport(), 'accounts.csv');
    }
}
