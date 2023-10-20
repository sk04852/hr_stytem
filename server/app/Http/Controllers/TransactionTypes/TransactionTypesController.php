<?php

namespace App\Http\Controllers\TransactionTypes;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Modules\Enums\LogAction;
use App\Http\Controllers\Modules\Enums\LogTypeEnum;
use App\Http\Controllers\Modules\Enums\ModuleEnum;
use App\Http\Controllers\TransactionTypes\Models\TransactionType as ThisModel;
use App\Http\Controllers\TransactionTypes\Requests\CreateTransactionTypeRequest as CreateRequest;
use App\Http\Controllers\TransactionTypes\Requests\UpdateTransactionTypeRequest as UpdateRequest;
use Illuminate\Http\Request;

class TransactionTypesController extends Controller
{
    const MODULE_NAME = 'transaction_type';
    const COLLECTION_NAME = 'transaction_types';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $records = $this->model->when(!empty($request->action), function ($query) use ($request) {
                return $query->where('action', $request->action);
            })->when(!empty($request->type), function ($query) use ($request) {
                $query->whereHas('type', function ($query) use ($request) {
                    $query->where('name', $request->type);
                });
            })
                ->with('type')->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            return $this->created([TransactionTypesController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                $this->addLog(LogTypeEnum::Info, null, $this->model, $this->model, LogAction::Created, ModuleEnum::TransactionType);
                return $this->created([TransactionTypesController::MODULE_NAME => $this->model, 'message' => TransactionTypesController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->model->with('type')->find($id);
            if (!empty($record)) {
                return $this->created([TransactionTypesController::MODULE_NAME => $record]);
            }
            return $this->created(["message" => TransactionTypesController::RECORD_NOT_FOUND]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $record = $this->model->find($request->id);
            $record->fill($request->all());
            $oldData = $record->getOriginal();
            if ($record->save()) {
                $this->addLog(LogTypeEnum::Info, null, $oldData, $record, LogAction::Updated, ModuleEnum::TransactionType);
                return $this->created([TransactionTypesController::MODULE_NAME => $record, 'message' => TransactionTypesController::RECORD_UPDATED]);
            }
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
                $this->addLog(LogTypeEnum::Info, null, $record, null, LogAction::Deleted, ModuleEnum::TransactionType);
                return $this->created(['message' => TransactionTypesController::RECORD_DELETED]);
            }
            return $this->created(["message" => TransactionTypesController::RECORD_NOT_FOUND]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
