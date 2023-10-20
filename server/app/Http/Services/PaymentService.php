<?php
namespace App\Http\Services;

use App\Models\Module;
use App\Http\Controllers\Clients\Models\Client;
use App\Http\Controllers\Payments\Models\Payment;

class PaymentService {

    private $clientModel_;
    private $transactionsService_;
    private $accountService_;
    public function __construct(
                                Client $client,
                                TransactionsService $transactionsService,
                                AccountingService $accountService)
    {
        $this->clientModel_ = $client;
        $this->transactionsService_ = $transactionsService;
        $this->accountService_ = $accountService;
    }

    public function getPaymentTransactionId(int $companyId, int $outletId, string $paymentId) {
        return $companyId . "-" . $outletId . "-" . $paymentId;
    }

    public function getTransactionsForPayment(Payment $payment) {
        $paymentTransactionId = $this->getPaymentTransactionId($payment->company_id, $payment->outlet_id, $payment->id);
        return $this->transactionsService_->fetchTransactionsByTransactionId($paymentTransactionId);
    }

    public function createTransactions($model, $paymentId, $outletId, $entity, $transactionsType, float $amount, string $note, int $companyId){
        $module = Module::find(getModuleIdFromEntity($model));
        $transactionsId = $this->getPaymentTransactionId($companyId, $outletId, $paymentId);
        $account = $this->UpdateAccount($companyId, $transactionsType, $amount);
        $this->transactionsService_->addTransaction($module,
                                                    $entity,
                                                    $transactionsId,
                                                    $transactionsType,
                                                    $amount,
                                                    $note,
                                                    $companyId,
                                                    $account->id);
    }

    public function UpdateAccount($companyId, $transactionsTypeId, $amount) {
        $account = $this->accountService_->getAccount($companyId, $transactionsTypeId);
        $account->update(['balance' => $account->balance + $amount]);
        return $account;
    }

    public function UpdateAccountWithDebit($companyId, $transactionsTypeId, $amount) {
        $account = $this->accountService_->getAccount($companyId, $transactionsTypeId);
        $account->update(['balance' => $account->balance - $amount]);
        return $account;
    }

    public function getTransactionTypeId($transactionType) {
      if ($transactionType == 'Debit') {
          return 1;
        }
      else if ($transactionType == 'Credit') {
          return 2;
        }
      else if ($transactionType == 'Cash') {
        return 3;
        }
    }
    public function updateAccountsAfterDeletion($transaction, $companyId) {
        $transactionIds = [];
        foreach ($transaction as $key => $values) {
            $transactionIds[$key] = $transaction[$key]->id;
            if ($transaction[$key]->credit == '0.00') {
                $this->updateAccountWithDebit(
                    $companyId,
                    $transaction[$key]->transaction_type_id,
                    $transaction[$key]->debit
                );
            } else {
                $this->updateAccount(
                    $companyId,
                    $transaction[$key]->transaction_type_id,
                    $transaction[$key]->credit
                );
            }
        }
        return $this->transactionsService_->deleteTransactions($transactionIds);
    }

}

?>
