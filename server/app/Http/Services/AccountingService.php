<?php
namespace App\Http\Services;

use Exception;
use App\Models\Module;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Accounts\Models\Account;
use App\Http\Controllers\AccountingAccountTypes\Models\AccountType;
use App\Http\Controllers\Modules\Enums\SaleTransactionTypeEnum;
use App\Http\Services\TransactionsService;

class AccountingService {

    CONST ACCOUNT_NOT_FOUNT = 'Company account does not exist';
    CONST CASH_ACCOUNT_ID = 1;
    CONST CUSTOMER_RECVEIVABLE_ACCOUNT = 2;
    CONST EMPLOYEE_RECVEIVABLE_ACCOUNT = 4;
    CONST INVENTORY_ACCOUNT = 3;

    private $transactionService_;
    private $companiesService_;

    public function __construct(
                                TransactionsService $transactionService,
                                CompaniesService $companiesService) {
        $this->transactionService_ = $transactionService;
        $this->companiesService_ = $companiesService;
    }

    public function getDefaultAccountTypeIds() {
        return AccountType::all();
    }

    public function getAccount($companyId, $transactionsTypeId, $createIfNotAvailable = false) {
        $data = [
            'company_id' => $companyId,
            'account_type_id' => $transactionsTypeId
        ];
        $account = Account::where($data)->first();
        if(!$account) {
            if($createIfNotAvailable) {
                $data['account_number'] = "COM-".$companyId."-".$transactionsTypeId;
                $account =  Account::create($data);
             }
            else throw new Exception("Account not found");
        }
        return $account;
    }

    public function allocateAccountsToCompany(int $companyId) {
        $company = $this->companiesService_->getCompany($companyId, []);
        if(!$company)
            throw new Exception("Unable to allocate accounts, company not found");
        $defaultAccounts = [];
        foreach($this->getDefaultAccountTypeIds() as $key=>$accountType) {
            $defaultAccounts[] = [
                                    'company_id'=> $companyId,
                                    'account_type_id'=> $accountType->id,
                                    'account_number'=> "COM-".$companyId."-".$accountType->id,
                                    'created_at'=> now(),
                                    'updated_at'=> now()
                                ];
        }
        Account::insert($defaultAccounts);
    }

    public function getCompanyAccounts(int $companyId) {
        return $this->companiesService_->getCompany($companyId)->accounts;
    }

    public function createTransactions($module,$entity, $transactionsId, $transactionsTypeId, float $amount, string $note, int $companyId){
        $this->transactionService_->addTransaction($module,$entity, $transactionsId, $transactionsTypeId, $amount, $note, $companyId, $this->getAccount($companyId,$transactionsTypeId)->id);
        $account = $this->getAccount($companyId, $transactionsTypeId);
        $account->update(['balance' => $account->balance + $amount]);
    }

    public function record(int $companyId, int $transactionType, Module $module, Model $entity, string $transactionId = null, float $amount = 0, string $note = null) {
        switch($transactionType) {
            case SaleTransactionTypeEnum::CreditSale:
                    $customerRecvableAccount = $this->getAccount($companyId, AccountingService::CUSTOMER_RECVEIVABLE_ACCOUNT);
                    $inventoryAccount = $this->getAccount($companyId, AccountingService::INVENTORY_ACCOUNT);
                    $this->transactionService_->quickCredit($companyId, $module, $entity, $transactionId, $amount, $customerRecvableAccount->id, $note);
                    $this->transactionService_->quickDebit($companyId, $module, $entity, $transactionId, $amount, $inventoryAccount->id, $note);
                    $customerRecvableAccount->update(['balance' => $customerRecvableAccount->balance + $amount]);
                    $inventoryAccount->update(['balance' => $inventoryAccount->balance - $amount]);
            break;

            case SaleTransactionTypeEnum::CashSale:
                $cashAccount = $this->getAccount($companyId, AccountingService::CASH_ACCOUNT_ID);
                $inventoryAccount = $this->getAccount($companyId, AccountingService::INVENTORY_ACCOUNT);
                $this->transactionService_->quickCredit($companyId, $module, $entity, $transactionId, $amount, $cashAccount->id, $note);
                $this->transactionService_->quickDebit($companyId, $module, $entity, $transactionId, $amount, $inventoryAccount->id, $note);
                $cashAccount->update(['balance' => $cashAccount->balance + $amount]);
                $inventoryAccount->update(['balance' => $inventoryAccount->balance - $amount]);
            break;

            case SaleTransactionTypeEnum::ClientPayment:
                $customerRecvableAccount = $this->getAccount($companyId, AccountingService::CUSTOMER_RECVEIVABLE_ACCOUNT);
                $cashAccount = $this->getAccount($companyId, AccountingService::CASH_ACCOUNT_ID);
                $this->transactionService_->quickCredit($companyId, $module, $entity, $transactionId, $amount, $cashAccount->id, $note);
                $this->transactionService_->quickDebit($companyId, $module, $entity, $transactionId, $amount, $customerRecvableAccount->id, $note);
                $customerRecvableAccount->update(['balance' => $customerRecvableAccount->balance - $amount]);
                $cashAccount->update(['balance' => $cashAccount->balance + $amount]);
            break;

            case SaleTransactionTypeEnum::EmployeeReceivable:
                $employeeReceivableAccount = $this->getAccount($companyId, AccountingService::EMPLOYEE_RECVEIVABLE_ACCOUNT, true);
                $cashAccount = $this->getAccount($companyId, AccountingService::CASH_ACCOUNT_ID);
                $this->transactionService_->quickCredit($companyId, $module, $entity, $transactionId, $amount, $cashAccount->id, $note);
                $this->transactionService_->quickDebit($companyId, $module, $entity, $transactionId, $amount, $employeeReceivableAccount->id, $note);
                $employeeReceivableAccount->update(['balance' => $employeeReceivableAccount->balance - $amount]);
                $cashAccount->update(['balance' => $cashAccount->balance + $amount]);
            break;

            default:
            break;
        }
    }

}

?>
