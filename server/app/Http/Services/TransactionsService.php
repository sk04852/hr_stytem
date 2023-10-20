<?php
namespace App\Http\Services;
use Exception;
use App\Models\Module;
use App\Exceptions\FaultyTransactionException;
use App\Exceptions\InvalidTransactionArgumentsException;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Transactions\Models\Transaction;
use App\Http\Controllers\TransactionTypes\Models\TransactionType;


class TransactionsService {
    const ALREADY_EXISTS = "This transaction id already exists";
	const SOMETHING_WRONG = "Data not saved, somethings went wrong";
	const DEBIT_SAVED = "Debit data saved";
	const QUICK_DEBIT_SAVED = "Quick debit data saved";
	const CREDIT_SAVED = "Credit data saved";
    const QUICK_CREDIT_SAVED = "Quick credit data saved";

    private $model_;

    public function __construct(Transaction $model) {
        $this->model_ = $model;
    }

    public function addTransaction(Module $module, Model $entity, string $transactionId, int $transactionTypeId, float $amount, string $note, int $companyId, int $accountId = null) {
            $relationModuleId = getModuleIdFromEntity($entity);
            $transactionSaved = false; 
			$transactionTypeIdData = TransactionType::where("id", $transactionTypeId)->first();

            // if($this->isDuplicateTransaction($module->id, $relationModuleId, $entity->id, $transactionId)) {
			// 	throw new Exception(TransactionsService::ALREADY_EXISTS);
            // }

			if($transactionTypeIdData->action == "Debit") {
				$transactionSaved = $this->saveTransection($module->id, $relationModuleId, $entity->id, $transactionId, $transactionTypeId, $amount, 0, $note, $companyId, $accountId);
			} else if($transactionTypeIdData->action == "Credit") {
				$transactionSaved = $this->saveTransection($module->id, $relationModuleId, $entity->id, $transactionId, $transactionTypeId, 0, $amount, $note, $companyId, $accountId);
            }else if($transactionTypeIdData->action == "Cash") {
				$transactionSaved = $this->saveTransection($module->id, $relationModuleId, $entity->id, $transactionId, $transactionTypeId, 0, $amount, $note, $companyId, $accountId);
            }

            if($transactionSaved)
                return true;
            else
                throw new Exception(TransactionsService::SOMETHING_WRONG);
    }

    public function saveTransection(int $transactionModuleId, int $relationModuleId, int $relationId, string $transactionId, int $transactionTypeId, float $dabit, float $credit, string $note = NULL, int $companyId, int $accountId = null) {
		$this->model_ = new Transaction();
        $this->model_->transaction_module_id = $transactionModuleId;
        $this->model_->relation_module_id = $relationModuleId;
        $this->model_->relation_id = $relationId;
        $this->model_->transaction_id = $transactionId;
        $this->model_->transaction_type_id = $transactionTypeId;
        $this->model_->debit = $dabit;
        $this->model_->credit = $credit;
        $this->model_->note = $note;
        $this->model_->company_id = $companyId;
        $this->model_->account_id = $accountId;
		return $this->model_->save();
	}

	public function quickDebit(int $companyId, Module $module, Model $entity, string $transactionId = null, float $amount = 0, int $accountId = -1, string $note = null) {

            if($entity == null || $entity->id == null) {
                throw new FaultyTransactionException("Unable to save transaction, Reason: Invalid entity provided for transaction");
            }

            if($transactionId == null) {
                throw new FaultyTransactionException("Unable to save transaction, Reason: Invalid transaction id");
            }

            $relationModuleId = getModuleIdFromEntity($entity);
            if($this->isDuplicateTransaction($module->id, $relationModuleId, $entity->id, $transactionId)) {
                throw new FaultyTransactionException("Unable to save transaction, Reason: Duplicate transaction.");
            }

			$transactionType = TransactionType::where("id", 1)->first();
            $note = ($note == null)? $transactionType->comment: $note;


            $relationModuleId = getModuleIdFromEntity($entity);
			$this->model_ = new Transaction();
			$this->model_->company_id = $companyId;
			$this->model_->transaction_module_id = $module->id;
            $this->model_->relation_module_id = $relationModuleId;
            $this->model_->relation_id = $entity->id;
            $this->model_->transaction_id = $transactionId;
            $this->model_->note = $note;
            $this->model_->transaction_type_id = $transactionType->id;
            $this->model_->debit = $amount;
            if($accountId > 0) {
                $this->model_->account_id = $accountId;
            }
			$this->model_->credit = 0;
			if(!$this->model_->save())
                throw new Exception(TransactionsService::ALREADY_EXISTS);
			else
                return true;
    }

    /*
        Module ID is the module this transaction belongs to.
        Relation Module ID is the module that the subject of this transaction belongs to for example a User
        Relation ID is the User ID in this case.
    */

    public function quickCredit(int $companyId, Module $module, Model $entity, string $transactionId = null, float $amount = 0, int $accountId = -1, string $note = null) {

        if($entity == null || $entity->id == null) {
            throw new FaultyTransactionException("Unable to save transaction, Reason: Invalid entity provided for transaction");
        }

        if($transactionId == null) {
            throw new FaultyTransactionException("Unable to save transaction, Reason: Invalid transaction id");
        }

        $relationModuleId = getModuleIdFromEntity($entity);
        if($this->isDuplicateTransaction($module->id, $relationModuleId, $entity->id, $transactionId)) {
            throw new FaultyTransactionException("Unable to save transaction, Reason: Duplicate transaction.");
        }

		$transactionType = TransactionType::where("id", 2)->first();
        if(empty($transactionType)){
            throw new FaultyTransactionException("Transaction type not found.");
        }
        $note = ($note == null)? $transactionType->comment: $note;

        $relationModuleId = getModuleIdFromEntity($entity);
        $this->model_ = new Transaction();
		$this->model_->company_id = $companyId;
        $this->model_->transaction_module_id = $module->id;
        $this->model_->relation_module_id = $relationModuleId;
        $this->model_->relation_id = $entity->id;
        $this->model_->transaction_id = $transactionId;
        $this->model_->note = $note;
        $this->model_->transaction_type_id = $transactionType->id;
        $this->model_->credit = $amount;

        if($accountId > 0) {
            $this->model_->account_id = $accountId;
        }
		$this->model_->debit = 0;
        if(!$this->model_->save())
            throw new Exception(TransactionsService::ALREADY_EXISTS);
        else
            return true;
    }

    public function isDuplicateTransaction(int $transactionModuleId, int $relationModuleId, int $relationId, string $transactionId) {
        return false;
        // return $this->model_->where(function($query) use($transactionModuleId, $relationModuleId, $relationId, $transactionId) {
        //     return $query->where('transaction_id', $transactionId)
        //                  ->where('transaction_module_id', $transactionModuleId)
        //                  ->where('relation_module_id', $relationModuleId)
        //                  ->where('relation_id', $relationId);
        // })->exists();
    }

    public function fetchTransactions(Module $module, Model $entity, int $transactionTypeId = null, int $numberOfTransactions = null) {
        $transactionType = null;

        if($transactionTypeId != null) {
            $transactionType = TransactionType::where("id", $transactionTypeId)->first();
        }

        if(is_null($module) || !isset($module->id)) {
            throw new InvalidTransactionArgumentsException("Invalid Module Id, unable to fetch transactions");
        }

        if(is_null($entity) || !isset($entity->id)) {
            throw new InvalidTransactionArgumentsException("Invalid entity, unable to fetch transactions");
        }

        $relationModuleId = getModuleIdFromEntity($entity);
        $relationId = $entity->id;
        $transactionModuleId = $module->id;
        $queryBuilder =
        $this->model_->where(function($query) use($transactionModuleId, $relationModuleId, $relationId, $transactionType){
            $query = $query->where('transaction_module_id', $transactionModuleId)
            ->where('relation_module_id', $relationModuleId)
            ->where('relation_id', $relationId);
            if(!is_null($transactionType)) {
                $query = $query->where('transaction_type_id', $transactionType->id);
            }
            return $query;
        });

        if(!is_null($numberOfTransactions)) {
            $queryBuilder->limit($numberOfTransactions);
        }

        return $queryBuilder->orderBy('created_at', 'DESC')->get();
    }

    public function fetchTransactionsByTransactionId(string $transactionId) {
        return $this->model_->where('transaction_id', $transactionId)->get();
    }

    public function fetchTransactionById(string $id) {
        return $this->model_->where('id', $id)->first();
    }

    public function fetchTransactionsByTransactionModule(int $transactionModuleId) {
        return $this->model_->where('transaction_module_id', $transactionModuleId)
        ->get();
    }

    public function deleteTransactions(array $transactionIds) {
        $this->model_->whereIn('id', $transactionIds)->delete();
    }



}
