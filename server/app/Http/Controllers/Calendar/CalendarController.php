<?php

namespace App\Http\Controllers\Calendar;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Calendar\Models\Calendar as ThisModel;
use App\Http\Controllers\Calendar\Requests\CreateCalendarRequest as CreateRequest;
use App\Http\Controllers\Calendar\Requests\MassDeleteCalendarRequest;
use App\Http\Controllers\Calendar\Requests\MassUpdateCalendarRequest;
use App\Http\Controllers\Calendar\Requests\UpdateCalendarRequest as UpdateRequest;
use App\Http\Controllers\Calendar\Requests\DeleteCalendarRequest as DeleteRequest;
use App\Http\Controllers\CalendarUser\Models\CalendarUser;
use App\Http\Controllers\PermissionDeniedException;
use Illuminate\Support\Facades\Request;
use App\Http\Services\CalendarService;

class CalendarController extends Controller
{

    const MODULE_NAME = 'calendar';
    const COLLECTION_NAME = 'calendars';
    const CALENDAR_DELETED = 'Calendar has been deleted successfuly';
    const CALENDARS_DELETED = 'Calendars has been deleted successfuly';
    const CALENDAR_NOT_DELETED = 'Error deleting calendar data';
    const CALENDAR_NOT_FOUND = 'Calendar not found';
    const CALENDARS_NOT_FOUND = 'Calendars not found';
    const CALENDAR_UPDATED = 'Calendar has been updated successfuly';
    const CALENDAR_UPDATED_FAILED = 'Error updating calendar data';
    const ENTER_UPDATED_DATA = 'Please provide data to be updated';
    private $calendarService_;

    public function __construct(
        ThisModel $model,
        CalendarService $calendarService) {
        parent::__construct($model);
        $this->calendarService_ = $calendarService;
    }

    public function index(){
        try {
            $records = $this->fetchUserCalendarsById($this->userId());
            if ($records->isEmpty()) {
                $this->calendarService_->createUserDefaultCalendar($this->userId());
            }
            return $this->created([CalendarController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    private function fetchUserCalendarsById(int $userId) {
        $filters = [];
        if (Request::has('title')) {
            $filters['title'] = Request::get('title');
        }
        if (Request::has('visibility')) {
            $filters['visibility'] = Request::get('visibility');
        }
        if (Request::has('created_by')) {
            $filters['created_by'] = Request::get('created_by');
        }

        $calendarsIds = CalendarUser::select('calendar_id')->where('user_id', $userId)->get();
        $records = $this->model->where('visibility', 'Public')
                                ->orWhere('created_by', $userId)
                                ->orWhereIn('id', $calendarsIds)
                                ->filter($filters)
                                ->orderBy($this->getSortBy(), $this->getSort())
                                ->paginate($this->getPerPage());
        return $records;
    }


    public function store(CreateRequest $request)
    {
        try {
            $this->model->fill($request->all());
            $this->model->created_by = $this->userId();
            if ($this->model->save()) {
                return $this->created([CalendarController::MODULE_NAME => $this->model, 'message'=> CalendarController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->model->find($id);
            if (!$record) {
                return $this->noRecord(['message'=>CalendarController::CALENDAR_NOT_FOUND]);
            } else {
                return $this->created([CalendarController::MODULE_NAME => $record]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request, int $id)
    {
        try {
            $record = $this->model->find($id);
            if (!$record) {
                return $this->noRecord(['message'=>CalendarController::CALENDAR_NOT_FOUND]);
            } else {
                $record->fill($request->all());
                if ($record->save()) {
                    return $this->created([CalendarController::MODULE_NAME => $record, 'message'=> CalendarController::RECORD_UPDATED]);
                }
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
                return $this->created(['message'=> CalendarController::RECORD_DELETED]);
            }
        }  catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function doMassDelete(MassDeleteCalendarRequest $request)
    {
        try {
            $data = $request->all();
            $CalendarIds = $data['id'];
            $records = $this->model->whereIn('id', $CalendarIds);
            if ($records->exists()) {
                if ($records->delete()) {
                    return $this->created(['message'=>CalendarController::CALENDARS_DELETED]);
                } else {
                    return $this->noRecord(['message'=>CalendarController::CALENDAR_NOT_DELETED]);
                }
            } else {
                return $this->noRecord(['message'=>CalendarController::CALENDARS_NOT_FOUND]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function doMassUpdate(MassUpdateCalendarRequest $request)
    {
        try {
            $allData = $request->all();
            $CalendarIds = $allData['id'];
            $updateData = $request->except(['id']);
            foreach ($CalendarIds as $key => $value) {
                $record = $this->model->find($CalendarIds[$key]);

                if (!$record == null) {

                    if (!empty($updateData['title'][$key])) {
                        $record->title = $updateData['title'][$key];
                    }
                    if (!empty($updateData['visibility'][$key])) {
                        $record->visibility = $updateData['visibility'][$key];
                    }
                    if (!empty($updateData['created_by'][$key])) {
                        $record->created_by = $updateData['created_by'][$key];
                    }
                    $record->save();
                } else {
                    return $this->noRecord(['message'=>CalendarController::CALENDAR_NOT_FOUND]);
                }
            }
            if ($record->save()){
                return $this->created(['message'=> CalendarController::CALENDAR_UPDATED]);
            } else {
                return $this->failed(['message'=> CalendarController::CALENDAR_UPDATED_FAILED]);
            }

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function searchByTitle($title)
    {
        try {
            $filteredCalendar = $this->model->where('title', 'like', "%$title%")
            ->orderBy($this->getSortBy(), $this->getSort())
            ->paginate($this->getPerPage());

            if (!$filteredCalendar->isEmpty()) {
                return $this->created([CalendarController::COLLECTION_NAME => $filteredCalendar]);
            } else {
                return $this->noRecord(['messsage'=> CalendarController::CALENDAR_NOT_FOUND]);
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