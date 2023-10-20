<?php

use App\Http\Services\AccountingService;
use App\Http\Controllers\AccountingAccountTypes\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountTypesSeeder extends Seeder
{
    private $accountingService_;
    public function __construct(AccountingService $accountingService) {
        $this->accountingService_ = $accountingService;
    }
    public function run()
    {
        $accountTypes = [
                         'Cash Account',
                         'Customer Receivable Account',
                         'Inventory Account'
                        ];
        for ($i = 0; $i < count($accountTypes); $i++) {
            $data [] = [
                'account_type' => $accountTypes[$i],
            ];
        }
        AccountType::insert($data);
        $this->accountingService_->allocateAccountsToCompany(1);
    }
}
