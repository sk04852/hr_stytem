<?php

namespace App\Http\Controllers\Currencies;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Currencies\Models\Currency as ThisModel;
use App\Http\Controllers\Currencies\Requests\CreateCurrencyRequest as CreateRequest;
use App\Http\Controllers\Currencies\Requests\UpdateCurrencyRequest as UpdateRequest;

class CurrenciesController extends Controller
{
    const MODULE_NAME = 'currency';
    const COLLECTION_NAME = 'currencies';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        try {
            $records = $this->retrieveRecords();
            return $this->created([CurrenciesController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if($this->model->save()) {
                return $this->created([CurrenciesController::MODULE_NAME => $this->model, 'message'=> CurrenciesController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->findOneById($id);
            return $this->created([CurrenciesController::MODULE_NAME => $record]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $record = $this->findOneById($request->currency_id);
            $record->fill($request->all());
            if($record->save()) {
                return $this->created([CurrenciesController::MODULE_NAME => $record, 'message'=> CurrenciesController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
