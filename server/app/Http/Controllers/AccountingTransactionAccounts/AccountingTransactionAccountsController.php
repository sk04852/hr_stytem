<?php

namespace App\Http\Controllers\AccountingTransactionAccounts;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Accounts\Models\Account as ThisModel;
use App\Http\Controllers\Accounts\Requests\CreateAccountRequest as CreateRequest;
use App\Http\Controllers\Accounts\Requests\UpdateAccountRequest as UpdateRequest;


class AccountingTransactionAccountsController extends Controller
{
    const MODULE_NAME = 'account';
    const COLLECTION_NAME = 'accounts';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        try {
            $records = $this->model->forThisCompany($this->companyId())->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            if($records->isNotEmpty()){
                return $this->created([AccountingTransactionAccountsController::COLLECTION_NAME => $records]);
            }
                return $this->noRecord(['message' => AccountingTransactionAccountsController::RECORD_NOT_FOUND, AccountingTransactionAccountsController::COLLECTION_NAME => $records],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $fillables = $this->model->getOnlyFillables($request->all());
            $fillables['company_id'] = $this->companyId();
            $isCreated = $this->model->create($fillables);
            if($isCreated) {
                return $this->created(['message'=> AccountingTransactionAccountsController::RECORD_CREATED]);
            }
                return response()->json(['message' => AccountingTransactionAccountsController::RECORD_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $fillables = $this->model->getOnlyFillables($request->all());
            $isUpdated = $this->model->where('id',$fillables['id'])->where('company_id',$this->companyId())->update($fillables);
            if($isUpdated) {
                return $this->created(['message'=> AccountingTransactionAccountsController::RECORD_UPDATED]);
            }
                return response()->json(['message' => AccountingTransactionAccountsController::RECORD_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try {
            $isFound = $this->model->where('id',$id)->where('company_id',$this->companyId())->first();
            if($isFound) {
                $isFound->delete();
                return $this->created(['message'=> AccountingTransactionAccountsController::RECORD_DELETED]);
            }
                return $this->noRecord(['message' => AccountingTransactionAccountsController::RECORD_NOT_FOUND],200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

}
