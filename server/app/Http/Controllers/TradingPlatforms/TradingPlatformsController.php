<?php

namespace App\Http\Controllers\TradingPlatforms;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Modules\Enums\LogAction;
use App\Http\Controllers\Modules\Enums\LogTypeEnum;
use App\Http\Controllers\Modules\Enums\ModuleEnum;
use App\Http\Controllers\TradingAccounts\TradingAccountsController;
use App\Http\Controllers\TradingPlatforms\Models\TradingPlatform as ThisModel;
use App\Http\Controllers\TradingPlatforms\Requests\CreateTradingPlatformsRequest as CreateRequest;
use App\Http\Controllers\TradingPlatforms\Requests\UpdateTradingPlatformsRequest as UpdateRequest;
use Illuminate\Http\Request;

class TradingPlatformsController extends Controller
{
    const MODULE_NAME = 'trading_platform';
    const COLLECTION_NAME = 'trading_platforms';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $records =  $this->model->forCompany($this->companyId())
                ->when(!empty($request->environment), function ($query) use ($request) {
                    return $query->where('environment', $request->environment);
                })
                ->when(!empty($request->login), function ($query) use ($request) {
                    return $query->where('login', $request->login);
                })
                ->when(!empty($request->port), function ($query) use ($request) {
                    return $query->where('port', $request->port);
                })
                ->when(!empty($request->host), function ($query) use ($request) {
                    return $query->where('host', 'LIKE' . '%' . $request->host . '%');
                })
                ->when(!empty($request->platform), function ($query) use ($request) {
                    return $query->where('platform', $request->platform);
                })
                ->when(!empty($request->created_by), function ($query) use ($request) {
                    return $query->where('created_by', $request->created_by);
                })
                ->when(!empty($request->last_updated_by), function ($query) use ($request) {
                    return $query->where('last_updated_by', $request->last_updated_by);
                })
                ->when(!empty($request->crm), function ($query) use ($request) {
                    return $query->where('crm', $request->crm);
                })
                ->when(!empty($request->port), function ($query) use ($request) {
                    return $query->where('port', $request->port);
                })
                ->withCompany()
                ->withEnvironment()
                ->withPlatform()
                ->withCRM()
                ->withCreatedBy()
                ->withLastUpdatedBy()
                ->orderBy($this->getSortBy(), $this->getSort())
                ->paginate($this->getPerPage());

            return $this->created([TradingPlatformsController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $this->model->fill($request->all());
            $this->model->company_id = $this->companyId();
            $this->model->created_by = $this->userId();
            $this->model->last_updated_by = $this->userId();
            if ($this->model->save()) {
                $this->addLog(LogTypeEnum::Info, null, $this->model, $this->model, LogAction::Created, ModuleEnum::TradingPlatforms);
                return $this->created([TradingPlatformsController::MODULE_NAME => $this->model, 'message' => TradingPlatformsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->model->where('id', $id)->forCompany($this->companyId())
                ->withCompany()
                ->withCreatedBy()
                ->withLastUpdatedBy()
                ->first();
            if (!$record)
                return $this->noRecord(['message' => TradingAccountsController::NO_RECORD], 404);
            return $this->created([TradingPlatformsController::MODULE_NAME => $record]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function edit($id)
    {
        return;
    }

    public function update(UpdateRequest $request)
    {
        try {
            $record = $this->model->where('id', $request->id)->forCompany($this->companyId())->first();
            if (!$record)
                return $this->noRecord(['message' => TradingAccountsController::NO_RECORD], 404);
            $this->model->last_updated_by = $this->userId();
            $record->fill($request->except(['created_by', 'company_id']));
            $oldData = $record->getOriginal();
            if ($record->save()) {
                $this->addLog(LogTypeEnum::Info, null, $oldData, $record, LogAction::Updated, ModuleEnum::TradingPlatforms);
                return $this->created([TradingPlatformsController::MODULE_NAME => $record, 'message' => TradingPlatformsController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try {
            $record = $this->model->where('id', $id)->forCompany($this->companyId())->first();
            if (!$record)
                return $this->noRecord(['message' => TradingAccountsController::NO_RECORD], 404);
            if ($record->delete()) {
                $this->addLog(LogTypeEnum::Info, null, $record, null, LogAction::Deleted, ModuleEnum::TradingPlatforms);
                return $this->created(['message' => TradingPlatformsController::RECORD_DELETED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
