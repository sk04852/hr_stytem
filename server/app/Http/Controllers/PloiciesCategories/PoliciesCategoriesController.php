<?php

namespace App\Http\Controllers\PloiciesCategories;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PloiciesCategories\Models\PoliciesCategory as ThisModel;
use App\Http\Controllers\PloiciesCategories\Requests\CreatePloiciesCategoryRequest as CreateRequest;
use App\Http\Controllers\PloiciesCategories\Requests\UpdatePloiciesCategoryRequest as UpdateRequest;

class PoliciesCategoriesController extends Controller
{
    const MODULE_NAME = 'policies_categories';
    const COLLECTION_NAME = 'policies_categories';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        try {
            $records = $this->retrieveRecords();
            return $this->created([PoliciesCategoriesController::COLLECTION_NAME => $records]);
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
            if($this->model->save()) {
                $this->addLog('info',$this->created,$this->modelt,PoliciesCategoriesController::MODULE_NAME,$this->type(),$this->userId());
                return $this->created([PoliciesCategoriesController::MODULE_NAME => $this->model, 'message'=> PoliciesCategoriesController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            $this->addLog('Error',$this->created,$ex->getMessage(),PoliciesCategoriesController::MODULE_NAME,'System','0');
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->findOneById($id);
            return $this->created([PoliciesCategoriesController::MODULE_NAME => $record]);
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
            $record = $this->findOneById($request->id);
            $record->fill($request->all());
            if($record->save()) {
                $this->addLog('info',$this->updated,$record,PoliciesCategoriesController::MODULE_NAME,$this->type(),$this->userId());
                return $this->created([PoliciesCategoriesController::MODULE_NAME => $record, 'message'=> PoliciesCategoriesController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            $this->addLog('Error',$this->updated,$ex->getMessage(),PoliciesCategoriesController::MODULE_NAME,'System','0');
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        return;
    }


}
