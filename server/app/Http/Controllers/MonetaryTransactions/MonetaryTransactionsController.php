<?php

namespace App\Http\Controllers\MonetaryTransactions;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Modules\Enums\LogAction;
use App\Http\Controllers\Modules\Enums\LogTypeEnum;
use App\Http\Controllers\Modules\Enums\ModuleEnum;
use App\Http\Controllers\MonetaryTransactions\Models\FinancialTransactionExtraInfo;
use App\Http\Controllers\MonetaryTransactions\Models\MonetaryTransaction as ThisModel;
use App\Http\Controllers\MonetaryTransactions\Requests\CreateMonetaryTransactionRequest as CreateRequest;
use App\Http\Controllers\TransactionTypes\Models\TransactionType;
use App\Http\Controllers\MonetaryTransactions\Requests\DeleteMassTransactionTypesRequest;
use App\Http\Controllers\TradingAccounts\PlatformDetails\Models\TradingAccountPlatformDetail;
use Illuminate\Http\Request;

class MonetaryTransactionsController extends Controller
{
    const MODULE_NAME = 'monetary_transaction';
    const COLLECTION_NAME = 'monetary_transactions';
    private $financialTransaction_;

    public function __construct(ThisModel $model, FinancialTransactionExtraInfo $financialTransaction)
    {
        parent::__construct($model);
        $this->financialTransaction_ = $financialTransaction;
    }

    public function index(Request $request)
    {
        try {
            $records = $this->model->monetaryTransactionsFilters($request)->with('financialDetail')
                ->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            return $this->created([MonetaryTransactionsController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $data = $request->all();
            if (empty($data['date'])) {
                $data['date'] = date('Y-m-d');
            }
            if (empty($data['crm_comment'])) {
                $transactionTypeComment = TransactionType::where('id', $data['transaction_type_id'])
                    ->select(['comment'])->first();
                if ($transactionTypeComment->comment !== NULL) {
                    $data['crm_comment'] = $transactionTypeComment->comment;
                }
            }
            $this->model->fill($data);
            if ($this->model->save()) {
                $this->addLog(LogTypeEnum::Info, null, $this->model, $this->model, LogAction::Created, ModuleEnum::MonetaryTransactions);
                $updateAccount = TradingAccountPlatformDetail::where('trading_account_id', $data['trading_account_id'])->first();
                if ($updateAccount) {
                    $updateAccount->update(['balance' => $updateAccount->balance + $data['amount']]);
                    $this->addLog(LogTypeEnum::Info, null, $updateAccount, $updateAccount, LogAction::Created, ModuleEnum::TradingAccounts);
                }
                $data['monetary_transaction'] = $this->model->id;
                $this->financialTransaction_->fill($data);
                $this->financialTransaction_->save();
                $this->addLog(LogTypeEnum::Info, null,  $this->financialTransaction_,  $this->financialTransaction_, LogAction::Created, ModuleEnum::FinancialTransactionExtraInfo);
                return $this->created([MonetaryTransactionsController::MODULE_NAME => $this->model, 'message' => MonetaryTransactionsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->model->with('financialDetail')->with('transactionType')->with('assignedTo')->find($id);
            if (!empty($record)) {
                return $this->created([MonetaryTransactionsController::MODULE_NAME => $record]);
            }
            return $this->created(["message" => MonetaryTransactionsController::RECORD_NOT_FOUND]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try {
            $record = $this->model->find($id);
            if ($record) {
                $record->delete();
                $this->addLog(LogTypeEnum::Info, null, $record, null, LogAction::Deleted, ModuleEnum::MonetaryTransactions);
                return $this->created(['message' => MonetaryTransactionsController::RECORD_DELETED]);
            }
            return $this->created(["message" => MonetaryTransactionsController::RECORD_NOT_FOUND]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function massDelete(DeleteMassTransactionTypesRequest $request)
    {
        try {
            $data = $request->all();
            $ids = $data['id'];
            $this->model->whereIn('id', $ids)->delete();
            return $this->created(['message' => MonetaryTransactionsController::MASS_RECORDS_DELETED]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
