<?php

namespace App\Http\Controllers\Sample;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Sample\Models\Sample as ThisModel;
use App\Http\Controllers\Sample\Requests\CreateSampleRequest as CreateRequest;
use App\Http\Controllers\Sample\Requests\UpdateSampleRequest as UpdateRequest;

class SampleController extends Controller
{
    const MODULE_NAME = 'sample';
    const COLLECTION_NAME = 'sample';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        try {
            $records = $this->retrieveRecords();
            return $this->created([SampleController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function create()
    {
        return;
    }

    public function store(CreateRequest $request)
    {
        try {
            $this->model->fill($request->all());
            $this->model->created_by = $this->userId();
            if($this->model->save()) {
                return $this->created([SampleController::MODULE_NAME => $this->model, 'message'=> SampleController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->findOneById($id);
            return $this->created([SampleController::MODULE_NAME => $record]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function edit($id)
    {
        return;
    }

    public function update(UpdateRequest $request, $id)
    {
        try {
            $auth = auth()->user();
            $record = $this->findOneById($id);
            $record->fill($request->all());
            if($record->save()) {
                return $this->created([SampleController::MODULE_NAME => $record, 'message'=> SampleController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        return;
    }


}
