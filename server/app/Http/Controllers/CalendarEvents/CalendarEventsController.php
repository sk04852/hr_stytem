<?php

namespace App\Http\Controllers\CalendarEvents;

use Exception;
use DateTime;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\CalendarEvents\Models\CalendarEvent as ThisModel;
use App\Http\Controllers\CalendarEvents\Models\EventsNotification;
use App\Http\Controllers\CalendarEvents\Requests\CreateCalendarEventRequest as CreateRequest;
use App\Http\Controllers\CalendarEvents\Requests\UpdateCalendarEventRequest as UpdateRequest;
use App\Http\Controllers\CalendarEvents\Requests\DeleteCalendarEventRequest as DeleteRequest;
use App\Http\Controllers\CalendarEvents\Requests\DeleteMassCalendarEventRequest as MassDeleteRequest;
use App\Http\Controllers\CalendarEvents\Requests\UpdateMassCalendarEventRequest as MassUpdateRequest;
use App\Http\Controllers\PermissionDeniedException;
use App\Http\Controllers\Users\Models\User;
use App\Notifications\CalendarEventNotification;
use App\Http\Services\CalendarService;

class CalendarEventsController extends Controller
{
    
    const MODULE_NAME = 'calendar_event';
    const COLLECTION_NAME = 'calendar_events';
    const DATE_FORMAT = "Y-m-d H:i:s";
    const CALENDAR_NOT_FOUND = "No records found";
    const NOTIFICATION_MAIL = 'Notification email has been sent';

    private $calendarService_ = NULL;

    public function __construct(ThisModel $model, CalendarService $calendarService)
    {
        parent::__construct($model);
        $this->calendarService_ = $calendarService;
    }

    public function index()
    {
        try {

            $records = $this->model->myEntries($this->userId())->orderBy($this->getSortBy(), $this->getSort())
                        ->paginate($this->getPerPage());
            return $this->created([CalendarEventsController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $event = $request->all();
            if(!$request->has('calendar_id')) {
                $userDefaultCalendar = $this->calendarService_->getUserDefaultCalendarById($this->userId());
                $event['calendar_id'] = $userDefaultCalendar->id;
            }

            if(!$request->has('status')) {
                $event['status'] = 'Not Held';
            }

            if(!$request->has('visibility')) {
                $event['visibility'] = 'Private';
            }

            $this->model->fill($event);
            $this->model->created_by = $this->userId();
            if($this->model->save()) {
                return $this->created([CalendarEventsController::MODULE_NAME => $this->model, 'message'=> CalendarEventsController::RECORD_CREATED]);
            }
        }  catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->findOneById($id);
            return $this->created([CalendarEventsController::MODULE_NAME => $record]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $id = $request->id;
            $record = $this->findOneById($id);
            $record->fill($request->all());
            if($record->save()) {
                return $this->created([CalendarEventsController::MODULE_NAME => $record, 'message'=> CalendarEventsController::RECORD_UPDATED]);
            }
        }catch (\Illuminate\Database\QueryException $ex) {
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
                return $this->created(['message'=> CalendarEventsController::RECORD_DELETED]);
            }
        }  catch (\Illuminate\Database\QueryException $ex) {
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
            return $this->created(['message'=>CalendarEventsController::MASS_RECORDS_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function massUpdate(MassUpdateRequest $request) {
        try {
            $events = $request->all();
            $eventIds = $events['id'];
            $updateData = $request->except(['id']);

            $startDateTimeArray = $events['start_date_time'];
            $endDateTimeArray = $events['end_date_time'];

            foreach($startDateTimeArray as $key => $value) {
                $startDate = (DateTime::createFromFormat(CalendarEventsController::DATE_FORMAT, $startDateTimeArray[$key]) !== false);
                $endDate = (DateTime::createFromFormat(CalendarEventsController::DATE_FORMAT, $endDateTimeArray[$key]) !== false);
                if(!$startDate || !$endDate) {
                    throw new Exception("Inavlid datetime");
                } else {
                    $startDateTs = strtotime($startDateTimeArray[$key]);
                    $endDateTs = strtotime($endDateTimeArray[$key]);
                    if($startDateTs >= $endDateTs)
                        throw new Exception("Inavlid datetime");
                }
            }
            
            $records = $this->findManyByIds($eventIds);
            foreach ($eventIds as $key => $value) {              
                $records->each(function($record) use($key,$value, $updateData) {
                    if($record->id == $value){
                        if($updateData['calendar_id'][$key]){
                            $record->calendar_id = $updateData['calendar_id'][$key];
                        }
                        if($updateData['subject'][$key]){
                            $record->subject = $updateData['subject'][$key];
                        }
                        if($updateData['type'][$key]){
                            $record->type = $updateData['type'][$key];
                        }
                        if($updateData['activity_type'][$key]){
                            $record->activity_type = $updateData['activity_type'][$key];
                        }
                        if($updateData['start_date_time'][$key]){
                            $record->start_date_time = $updateData['start_date_time'][$key];
                        }
                        if($updateData['end_date_time'][$key]){
                            $record->end_date_time = $updateData['end_date_time'][$key];
                        }
                        if($updateData['visibility'][$key]){
                            $record->visibility = $updateData['visibility'][$key];
                        }
                        if($updateData['status'][$key]){
                            $record->status = $updateData['status'][$key];
                        }
                        if($updateData['assigned_to'][$key]){
                            $record->assigned_to = $updateData['assigned_to'][$key];
                        }
                       $record->save();
                    }
                });

            } 
            return $this->created([$this->model->whereIn('id',$eventIds)->get(), 'message'=> CalendarEventsController::MASS_RECORDS_UPDATED]);
            
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    
    public function search(string $keywords)
    {
        try {
            $userEntryIds = $this->model->myEntries($this->userId())->select(['id'])->get();
            $calendarEntries = $this->model->simpleSearch(['subject', 'description'], $keywords)->whereIn('id', $userEntryIds)->get();
            if (!$calendarEntries->isEmpty()) {
                return $this->created([CalendarEventsController::COLLECTION_NAME => $calendarEntries]);
            } else {
                return $this->noRecord(['messsage'=> CalendarEventsController::CALENDAR_NOT_FOUND]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function sendEmail($id , $event )
    {
        try 
        {
            $eventNotification = new EventsNotification;
            $eventNotification->user_id = $id;
            $eventNotification->event_id = $event->id;
            $eventNotification->save();

            return $this->created(['message'=> CalendarEventsController:: NOTIFICATION_MAIL]);


            $user = User::where('id',$id)->first();
            $NotificationData = [
                'full_name' => $user->getFullName(),
                'event_title' => $event->subject,
                'start_time' => $event->start_date_time,
                'end_time' => $event->end_date_time,
            ];
            $user->notify(new CalendarEventNotification($NotificationData , $user->email));
            $eventNotification = new EventsNotification;
            $eventNotification->user_id = $id;
            $eventNotification->event_id = $event->id;
            $eventNotification->save();

            return $this->created(['message'=> CalendarEventsController:: NOTIFICATION_MAIL]);
        }
        catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    public function scheduler()
    {
        try {
            $nowDate = Carbon::instance(new \DateTime());
            $fifteenAfter = Carbon::instance(new \DateTime());
            $fifteenAfter = $fifteenAfter->addMinutes(15);

            $events = $this->model->whereBetween('start_date_time', array($nowDate , $fifteenAfter))->get();
            foreach($events as $event){
                if(!is_null($event->assigned_to))
                {
                    if(!EventsNotification::where('event_id',$event->id)->where('user_id',$event->assigned_to)->exists()){
                        $this->sendEmail($event->assigned_to , $event);
                    }   
                }
                if(!EventsNotification::where('event_id',$event->id)->where('user_id',$event->created_by)->exists()){
                    $this->sendEmail($event->created_by , $event);
                }   
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}