<?php

namespace App\Http\Controllers\BankAccount;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BankAccount\Models\BankAccount as ThisModel;
use App\Http\Controllers\BankAccount\Models\BankAccount;
use App\Http\Controllers\BankAccount\Requests\CreateBankAccountRequest as CreateRequest;
use App\Http\Controllers\BankAccount\Requests\UpdateBankAccountRequest as UpdateRequest;
use App\Http\Controllers\BankAccount\Requests\DeleteBankAccountRequest as DeleteRequest;
use App\Http\Controllers\BankAccount\Requests\MassDeleteBankAccountRequest;
use App\Http\Controllers\BankAccount\Requests\MassUpdateBankAccountRequest;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    const MODULE_NAME = 'BankAccount';
    const COLLECTION_NAME = 'BankAccounts';
    const BANKACCOUNT_DELETED = 'BankAccount has been deleted successfuly';
    const BANKACCOUNTS_DELETED = 'BankAccounts has been deleted successfuly';
    const BANKACCOUNT_NOT_DELETED = 'Error Deleting BankAccount Data';
    const BANKACCOUNT_NOT_FOUND = 'BankAccount Not Found';
    const BANKACCOUNTS_NOT_FOUND = 'BankAccounts Not Found';
    const BANKACCOUNT_UPDATED = 'BankAccount has been updated successfuly';
    const BANKACCOUNT_UPDATED_FAILED = 'Error Updating BankAccount Data';
    const ENTER_UPDATED_DATA = 'Please Enter Data which is to be updated';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $record = BankAccount::when(!empty($request->bank_id), function ($query) use ($request) {
                return $query->where('bank_id', $request->bank_id);
            })
            ->when(!empty($request->account_number), function ($query) use ($request) {
                return $query->where('account_number', $request->account_number);
            })
            ->when(!empty($request->iban), function ($query) use ($request) {
                return $query->where('iban', $request->iban);
            })
            ->when(!empty($request->branch_code), function ($query) use ($request) {
                return $query->where('branch_code', $request->branch_code);
            })
            ->when(!empty($request->swift_code), function ($query) use ($request) {
                return $query->where('swift_code', $request->swift_code);
            })
            ->when(!empty($request->bic), function ($query) use ($request) {
                return $query->where('bic', $request->bic);
            })
            ->when(!empty($request->currency), function ($query) use ($request) {
                return $query->where('currency', $request->currency);
            })
            ->orderBy('id', 'DESC')->paginate(20);
            if ($record->isNotEmpty()) {
                return $this->created([BankAccountController::COLLECTION_NAME => $record]);
            }
            return $this->noRecord(['message' => BankAccountController::RECORD_NOT_FOUND]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $bank_account = new BankAccount();
            $bank_account->bank_id = $request->bank_id;
            $bank_account->account_number = $request->account_number;
            $bank_account->iban = $request->iban;
            $bank_account->branch_code = $request->branch_code;
            $bank_account->swift_code = $request->swift_code;
            $bank_account->bic = $request->bic;
            $bank_account->currency = $request->currency;
            $bank_account->save();
            if ($bank_account->save()) {
                return $this->created(['message' => BankAccountController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = BankAccount::find($id);
            if (!$record) {
                return $this->noRecord(['message' => BankAccountController::BANKACCOUNT_NOT_FOUND]);
            } else {
                return $this->created([BankAccountController::MODULE_NAME => $record]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $data = BankAccount::where('id', $request->id)->update($request->all());
            if ($data) {
                return $this->created(['message' => BankAccountController::BANKACCOUNT_UPDATED]);
            } else {
                return $this->failed(['message' => BankAccountController::BANKACCOUNT_UPDATED_FAILED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteRequest $request)
    {
        try {
            $id = $request->id;
            $record = $this->findOneById($id);
            if ($record->delete()) {
                return $this->created(['message' => BankAccountController::RECORD_DELETED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function doMassDelete(MassDeleteBankAccountRequest $request)
    {
        try {
            $data = $request->all();
            $BankAccountIds = $data['id'];
            $records = BankAccount::whereIn('id', $BankAccountIds);
            if ($records->exists()) {
                if ($records->delete()) {
                    return $this->created(['message' => BankAccountController::BANKACCOUNTS_DELETED]);
                } else {
                    return $this->noRecord(['message' => BankAccountController::BANKACCOUNT_NOT_DELETED]);
                }
            } else {
                return $this->noRecord(['message' => BankAccountController::BANKACCOUNTS_NOT_FOUND]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function doMassUpdate(MassUpdateBankAccountRequest $request)
    {
        try {
            $allData = $request->all();
            $BankAccountIds = $allData['id'];
            $updateData = $request->except(['id']);
            foreach ($BankAccountIds as $key => $value) {
                $record = BankAccount::find($BankAccountIds[$key]);

                if (!$record == null) {
                    if (!empty($updateData['bank_id'][$key])) {
                        $record->bank_id = $updateData['bank_id'][$key];
                    }
                    if (!empty($updateData['account_number'][$key])) {
                        $record->account_number = $updateData['account_number'][$key];
                    }
                    if (!empty($updateData['iban'][$key])) {
                        $record->iban = $updateData['iban'][$key];
                    }
                    if (!empty($updateData['branch_code'][$key])) {
                        $record->branch_code = $updateData['branch_code'][$key];
                    }
                    if (!empty($updateData['swift_code'][$key])) {
                        $record->swift_code = $updateData['swift_code'][$key];
                    }
                    if (!empty($updateData['bic'][$key])) {
                        $record->bic = $updateData['bic'][$key];
                    }
                    if (!empty($updateData['currency'][$key])) {
                        $record->currency = $updateData['currency'][$key];
                    }
                    $record->save();
                } else {
                    return $this->noRecord(['message' => BankAccountController::BANKACCOUNT_NOT_FOUND]);
                }
            }
            if ($record->save()) {
                return $this->created(['message' => BankAccountController::BANKACCOUNT_UPDATED]);
            } else {
                return $this->failed(['message' => BankAccountController::BANKACCOUNT_UPDATED_FAILED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function searchByAccountNumber($account_number)
    {
        try {
            $filteredBankAccount = BankAccount::where('account_number', "$account_number")->get();

            if (!$filteredBankAccount->isEmpty()) {
                return $this->created([BankAccountController::COLLECTION_NAME => $filteredBankAccount]);
            } else {
                return $this->noRecord(['messsage' => BankAccountController::BANKACCOUNT_NOT_FOUND]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function edit($id)
    {
        return;
    }

    public function create()
    {
        return;
    }
}
