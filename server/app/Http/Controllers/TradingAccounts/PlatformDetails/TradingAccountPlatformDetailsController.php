<?php

namespace App\Http\Controllers\TradingAccounts\PlatformDetails;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TradingAccounts\PlatformDetails\Models\TradingAccountPlatformDetail as ThisModel;
use App\Http\Controllers\TradingAccounts\PlatformDetails\Requests\PlatformDetailsCreateRequest as CreateRequest;
use App\Http\Controllers\TradingAccounts\PlatformDetails\Requests\PlatformDetailsUpdateRequest as UpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class TradingAccountPlatformDetailsController extends Controller
{
    const MODULE_NAME = 'trading_account_platfor_details';
    const COLLECTION_NAME = 'trading_account_platform_details';

    public function __construct(ThisModel $model)

    {
        parent::__construct($model);
    }

    public function index()
    {
        try {
            $records = $this->retrieveRecords();
            return $this->created([TradingAccountPlatformDetailsController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $tradingPlatformDetailsData = $request->all();
            if($this->createTradingPlatformDetails($tradingPlatformDetailsData)) {
                return $this->created([TradingAccountPlatformDetailsController::MODULE_NAME => $this->model, 'message'=> TradingAccountPlatformDetailsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function createTradingPlatformDetails(array $tradingPlatformDetailsData) {
        $this->model->fill($tradingPlatformDetailsData);
        return $this->model->save();
    }

    public function updateTradingPlatformDetails(int $id, array $tradingPlatformDetailsData) {
        $this->model = $this->findOneById($id);
        return $this->model->update($tradingPlatformDetailsData);
    }

    public function show(int $id)
    {
        try {
            $record = $this->findOneById($id);
            return $this->created([TradingAccountPlatformDetailsController::MODULE_NAME => $record]);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return $this->noRecord(["message"=> TradingAccountPlatformDetailsController::RECORD_NOT_FOUND]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request, int $id)
    {
        try {
            $tradingPlatformData = $request->all();
            if($this->updateTradingPlatformDetails($id, $tradingPlatformData)) {
                return $this->created([TradingAccountPlatformDetailsController::MODULE_NAME => $this->model, 'message'=> TradingAccountPlatformDetailsController::RECORD_UPDATED]);
            }
        } catch (ModelNotFoundException $modelNotFoundException) {
            return $this->noRecord(["message"=> TradingAccountPlatformDetailsController::RECORD_NOT_FOUND]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(int $id)
    {
        try {
            $record = $this->findOneById($id);
            if($record->delete()) {
                return $this->created(['message'=> TradingAccountPlatformDetailsController::RECORD_DELETED]);
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
