<?php

namespace App\Http\Controllers\EventUsers;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EventUsers\Models\EventUser as ThisModel;
use Illuminate\Support\Facades\Request;

use App\Http\Controllers\EventUsers\Requests\CreateEventUserRequest as CreateRequest;
use App\Http\Controllers\EventUsers\Requests\UpdateEventUserRequest as UpdateRequest;
use App\Http\Controllers\EventUsers\Requests\DeleteEventUserRequest as DeleteRequest;
use App\Http\Controllers\EventUsers\Requests\DeleteMassEventUserRequest as MassDeleteRequest;
use App\Http\Controllers\EventUsers\Requests\UpdateMassEventUserRequest as MassUpdateRequest;

class EventUsersController extends Controller
{
    
    const MODULE_NAME = 'event_user';
    const COLLECTION_NAME = 'event_users';
    const ALREADY_EXISTS = 'This User Already has the Permission';
    

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        try {
            $records = $this->retrieveRecords();
            return $this->created([EventUsersController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $duplicate=$this->model->where('user_id',$request->user_id)->where('event_id',$request->event_id)->exists();
            if($duplicate){
                return $this->created(['message'=> EventUsersController::ALREADY_EXISTS]);
            }
            $this->model->fill($request->all());
            if($this->model->save()) {
                return $this->created([EventUsersController::MODULE_NAME => $this->model, 'message'=> EventUsersController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->findOneById($id);
            return $this->created([EventUsersController::MODULE_NAME => $record]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $duplicate=$this->model->where('user_id',$request->user_id)->where('event_id',$request->event_id)->exists();
            if($duplicate){
                return $this->created(['message'=> EventUsersController::ALREADY_EXISTS]);
            }
            $id = $request->id;
            $record = $this->findOneById($id);
            $record->fill($request->all());
            if($record->save()) {
                return $this->created([EventUsersController::MODULE_NAME => $record, 'message'=> EventUsersController::RECORD_UPDATED]);
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
                return $this->created(['message'=> EventUsersController::RECORD_DELETED]);
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

    public function massDelete(MassDeleteRequest $request) {
        try {
            $data = $request->all();
            $ids = $data['id'];
            $this->model->whereIn('id',$ids)->delete();
            return $this->created(['message'=>EventUsersController::MASS_RECORDS_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function massUpdate(MassUpdateRequest $request) {
        try {
            $events = $request->all();
            $eventsIds = $events['id'];
            $updateData = $request->except(['id']);
            $records = $this->findManyByIds($eventsIds);

            foreach ($eventsIds as $key => $value) {
                $duplicate = $this->model->where('user_id',$updateData['user_id'][$key])->where('event_id',$updateData['event_id'][$key])->exists();
                if($duplicate){
                    return $this->created(['ID'=> $value,'User ID'=>$updateData['user_id'][$key],'message'=> EventUsersController::ALREADY_EXISTS]);
                }
                $records->each(function($record) use($key,$value, $updateData){
                        if($record->id == $value){
                            if($updateData['event_id'][$key]){
                            $record->event_id = $updateData['event_id'][$key];
                            }
                            if($updateData['user_id'][$key]){
                            $record->user_id = $updateData['user_id'][$key];
                            }
                            $record->save();
                        }
                });
            } 
            return $this->created([$this->findManyByIds($eventsIds) , 'message'=> EventUsersController::MASS_RECORDS_UPDATED]);
            
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

