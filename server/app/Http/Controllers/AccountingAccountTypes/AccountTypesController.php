<?php

namespace App\Http\Controllers\AccountingAccountTypes;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AccountingAccountTypes\Models\AccountType as ThisModel;
use App\Http\Controllers\AccountingAccountTypes\Requests\CreateAccountTypeRequest as CreateRequest;
use App\Http\Controllers\AccountingAccountTypes\Requests\UpdateAccountTypeRequest as UpdateRequest;
use Illuminate\Http\Request;

class AccountTypesController extends Controller
{
    const MODULE_NAME = 'account_type';
    const COLLECTION_NAME = 'account_types';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $records = $this->model->accountTypes($request)->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            if($records->isNotEmpty()){
                return $this->created([AccountTypesController::COLLECTION_NAME => $records]);
            }
                return $this->noRecord(['message' => AccountTypesController::RECORD_NOT_FOUND, AccountTypesController::COLLECTION_NAME => $records],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $fillables = $this->model->getOnlyFillables($request->all());
            $isCreated = $this->model->create($fillables);
            if($isCreated) {
                return $this->created(['message'=> AccountTypesController::RECORD_CREATED]);
            }
                return response()->json(['message' => AccountTypesController::RECORD_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $fillables = $this->model->getOnlyFillables($request->all());
            $isUpdated = $this->model->where('id',$fillables['id'])->update($fillables);
            if($isUpdated) {
                return $this->created(['message'=> AccountTypesController::RECORD_UPDATED]);
            }
                return response()->json(['message' => AccountTypesController::RECORD_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try {
            $isFound = $this->model->find($id);
            if($isFound) {
                $isFound->delete();
                return $this->created(['message'=> AccountTypesController::RECORD_DELETED]);
            }
                return $this->noRecord(['message' => AccountTypesController::RECORD_NOT_FOUND],200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

}
