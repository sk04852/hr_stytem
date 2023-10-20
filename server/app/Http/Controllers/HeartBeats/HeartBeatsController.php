<?php

namespace App\Http\Controllers\HeartBeats;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HeartBeats\Models\HeartBeat as ThisModel;
use App\Http\Controllers\HeartBeats\Requests\CreateHeartBeatRequest as CreateRequest;
use App\Http\Controllers\HeartBeats\Requests\UpdateHeartBeatRequest as UpdateRequest;
use App\Http\Controllers\HeartBeats\Requests\DeleteHeartBeatRequest as DeleteRequest;
use App\Http\Controllers\HeartBeats\Requests\DeleteHeartBeatRequest as CheckRequest;
use App\Http\Services\HeartBeatService;

class HeartBeatsController extends Controller
{
    const MODULE_NAME = 'heart_beat';
    const COLLECTION_NAME = 'heart_beats';
    private $heartBeatService_;

    public function __construct(ThisModel $model, HeartBeatService $heartBeatService)
    {
        $this->heartBeatService_ = $heartBeatService;
        parent::__construct($model);
    }

    public function index()
    {
        try {
            $records = $this->model->orderBy($this->getSortBy(), $this->getSort())
                                   ->paginate($this->getPerPage());
            if(!$records->isNotEmpty()){
                return $this->noRecord(['message' => HeartBeatsController::RECORD_NOT_FOUND],200); 
            }
            return $this->created([HeartBeatsController::COLLECTION_NAME => $records]);
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
                return $this->created([HeartBeatsController::MODULE_NAME => $this->model, 'message'=> HeartBeatsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->model->find($id);
            if(!$record){
                return $this->noRecord(['message' => HeartBeatsController::RECORD_NOT_FOUND],200);
            }
            return $this->created([HeartBeatsController::COLLECTION_NAME => $record]);
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
            $record = $this->model->update($request->all());
            if($record) {
                return $this->created([HeartBeatsController::MODULE_NAME => $record, 'message'=> HeartBeatsController::RECORD_UPDATED]);
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
            if($record->delete()) {
                return $this->created(['message'=> HeartBeatsController::RECORD_DELETED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    
    public function checkHeartBeat(CheckRequest $request)
    {
        try {
            $this->heartBeatService_->checkHeartBeat($request->id);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
