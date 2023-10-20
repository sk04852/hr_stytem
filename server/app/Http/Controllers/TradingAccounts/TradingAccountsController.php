<?php

namespace App\Http\Controllers\TradingAccounts;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TradingAccounts\Models\TradingAccount as ThisModel;
use App\Http\Controllers\TradingAccounts\PlatformDetails\TradingAccountPlatformDetailsController;
use App\Http\Controllers\TradingAccounts\Requests\TradingAccountCreateRequest as CreateRequest;
use App\Http\Controllers\TradingAccounts\Requests\TradingAccountUpdateRequest as UpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class TradingAccountsController extends Controller
{
    const MODULE_NAME = 'trading_account';
    const COLLECTION_NAME = 'trading_accounts';
    private $defaultPlatform_ = 'MT5';
    private $tradingAccountPlatformDetailsController_;

    public function __construct(
            ThisModel $model,
            TradingAccountPlatformDetailsController $tradingAccountPlatformDetailsController){
        parent::__construct($model);
        $this->tradingAccountPlatformDetailsController_ = $tradingAccountPlatformDetailsController;
    }

    public function index()
    {
        try {
            $records = $this->retrieveRecords();
            return $this->created([TradingAccountsController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $tradingAccountData = [
                                    'tp_account_id' => $request->get('tp_account_id'),
                                    'account_id' => $request->get('account_id'),
                                    'currency' => $request->get('currency','USD'),
                                    'created_by' => $this->userId(),
                                    'assigned_to' => $request->get('assigned_to', $this->userId()),
                                    'platform' => $request->get('platform', $this->getDefaultPlatform()),
                                    'account_stage' => $request->get('account_stage', 'Registered'),
                                    'ib' => $request->get('ib', '0'),
                                    'trading_platform_group' => $request->get('trading_platform_group', 'Default'),
                                    'is_additional_account' =>$request->get('is_additional_account', "No")];
            if($this->createTradingAccount($tradingAccountData)) {
                $tradingPlatformDetailsData = $request->get('trading_platform_details');
                $tradingPlatformDetailsData['trading_account_id'] = $this->getLastCreatedEntity()->id;
                $this->tradingAccountPlatformDetailsController_->createTradingPlatformDetails($tradingPlatformDetailsData);
                $this->model->setTradingPlatformDetails($this->tradingAccountPlatformDetailsController_->getLastCreatedEntity());
                return $this->created([TradingAccountsController::MODULE_NAME => $this->model, 'message'=> TradingAccountsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function createTradingAccount(array $tradingAccountData) {
        $this->model->fill($tradingAccountData);
        return $this->model->save();
    }

    public function updateTradingAccount(int $id, array $tradingPlatformData) {
        $this->model = $this->findOneById($id);
        return $this->model->update($tradingPlatformData);
    }

    public function show(int $id)
    {
        try {
            $record = $this->findOneById($id);
            return $this->created([TradingAccountsController::MODULE_NAME => $record]);
        } catch(ModelNotFoundException $modelNotFoundException) {
            return $this->noRecord(["message"=> TradingAccountsController::RECORD_NOT_FOUND]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request, int $id)
    {
        try {
            $tradingPlatformData = $request->all();
            if($this->updateTradingAccount($id, $tradingPlatformData)) {
                return $this->created([TradingAccountsController::MODULE_NAME => $this->model, 'message'=> TradingAccountsController::RECORD_UPDATED]);
            }
        } catch (ModelNotFoundException $modelNotFoundException) {
            return $this->noRecord(["message"=> TradingAccountsController::RECORD_NOT_FOUND]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(int $id)
    {
        try {
            $record = $this->findOneById($id);
            if($record->delete()) {
                return $this->created(['message'=> TradingAccountsController::RECORD_DELETED]);
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

    public function getDefaultPlatform() {
        return $this->defaultPlatform_;
    }

    public function setDefaultPlatform(string $platform) {
        return $this->defaultPlatform_ = $platform;
    }
}
