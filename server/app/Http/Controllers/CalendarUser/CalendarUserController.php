<?php

namespace App\Http\Controllers\CalendarUser;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CalendarUser\Models\CalendarUser as ThisModel;
use App\Http\Controllers\CalendarUser\Models\CalendarUser;
use App\Http\Controllers\CalendarUser\Requests\CreateCalendarUserRequest as CreateRequest;
use App\Http\Controllers\CalendarUser\Requests\MassDeleteCalendarUserRequest;
use App\Http\Controllers\CalendarUser\Requests\UpdateCalendarUserRequest as UpdateRequest;
use App\Http\Controllers\CalendarUser\Requests\DeleteCalendarUserRequest as DeleteRequest;
use App\Http\Controllers\CalendarUser\Requests\CalendarUserMassUpdateRequest;
use Illuminate\Http\Request;

class CalendarUserController extends Controller
{

    const MODULE_NAME = 'CalendarUser';
    const COLLECTION_NAME = 'CalendarUsers';
    const CALENDARUSER_DELETED = 'CalendarUser has been deleted successfuly';
    const CALENDARUSERS_DELETED = 'CalendarUsers has been deleted successfuly';
    const CALENDARUSER_NOT_DELETED = 'Error Deleting CalendarUser Data';
    const CALENDARUSER_NOT_FOUND = 'CalendarUser Not Found';
    const CALENDARUSERS_NOT_FOUND = 'CalendarUsers Not Found';
    const CALENDARUSER_UPDATED = 'CalendarUser has been updated successfuly';
    const CALENDARUSER_UPDATED_FAILED = 'Error Updating CalendarUser Data';
    const ENTER_UPDATED_DATA = 'Please Enter Data which is to be updated';


    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {

        try {

            $record = CalendarUser::when( !empty($request->user_id), function($query) use($request) {
                return $query->where('user_id', $request->user_id);
            }) 
            ->when( !empty($request->calendar_id), function($query) use($request) {
                return $query->where('calendar_id', $request->calendar_id);
            })      
            ->orderBy('id','DESC')->paginate(20);
            if ( $record->isNotEmpty() ) {        
                return $this->created([CalendarUserController::COLLECTION_NAME => $record]);
            }
                return $this->noRecord(['message'=>CalendarUserController::RECORD_NOT_FOUND]);
        }catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
           $calendarUser = new CalendarUser();
           $calendarUser->user_id = $request->user_id;
           $calendarUser->calendar_id = $request->calendar_id;
           $calendarUser->save();
              if ($calendarUser->save()) {
                   return $this->created(['message'=> CalendarUserController::RECORD_CREATED]);
                  }

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = CalendarUser::find($id);
            if (!$record) {
                return $this->noRecord(['message'=>CalendarUserController::CALENDARUSER_NOT_FOUND]);
            } else {
                return $this->created([CalendarUserController::MODULE_NAME => $record]);
        }
        }  catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
                $data= CalendarUser::where('id',$request->id)->update($request->all());
                if($data){
                return $this->created(['message'=> CalendarUserController::CALENDARUSER_UPDATED]);

             }else{
                return $this->failed(['message'=> CalendarUserController::CALENDARUSER_UPDATED_FAILED]);
             }
            }
         catch (\Illuminate\Database\QueryException $ex) {
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
                return $this->created(['message'=> CalendarUserController::RECORD_DELETED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function doMassDelete(MassDeleteCalendarUserRequest $request)
    {
        try {
            $data = $request->all();
            $CalendarUserIds = $data['id'];
            $records = CalendarUser::whereIn('id', $CalendarUserIds);
            if ($records->exists()) {
                if ($records->delete()) {
                    return $this->created(['message'=>CalendarUserController::CALENDARUSERS_DELETED]);
                } else {
                    return $this->noRecord(['message'=>CalendarUserController::CALENDARUSER_NOT_DELETED]);
                }
            } else {
                return $this->noRecord(['message'=>CalendarUserController::CALENDARUSERS_NOT_FOUND]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function doMassUpdate(CalendarUserMassUpdateRequest $request)
    {
        try {
            $allData = $request->all();
            $CalendarUserIds = $allData['id'];
            $updateData = $request->except(['id']);
            foreach ($CalendarUserIds as $key => $value) {
                $record = CalendarUser::find($CalendarUserIds[$key]);

                if (!$record == null) {

                    if (!empty($updateData['user_id'][$key])) {
                        $record->user_id = $updateData['user_id'][$key];
                    }
                    if (!empty($updateData['calendar_id'][$key])) {
                        $record->calendar_id = $updateData['calendar_id'][$key];
                    }
                    $record->save();
                } else {
                    return $this->noRecord(['message'=>CalendarUserController::CALENDARUSER_NOT_FOUND]);
                }
            }
            if ($record->save()){
                return $this->created(['message'=> CalendarUserController::CALENDARUSER_UPDATED]);
            } else {
                return $this->failed(['message'=> CalendarUserController::CALENDARUSER_UPDATED_FAILED]);
            }

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function searchByUserId($user_id)
    {
        try {
            $filteredCalendarUser = CalendarUser::where('user_id', "$user_id")->get();

            if (!$filteredCalendarUser->isEmpty()) {
                return $this->created([CalendarUserController::COLLECTION_NAME => $filteredCalendarUser]);
            } else {
                return $this->noRecord(['messsage'=> CalendarUserController::CALENDARUSER_NOT_FOUND]);
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