<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Calendar\Models\Calendar;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CalendarEvents\Models\CalendarEvent;
use App\Http\Controllers\Users\Models\User;
use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Illuminate\Http\Request;


class gCalendarController extends Controller
{
    const EVENT_DATA_SYNCED = 'The calendar events data has been synced';
    const CALENDAR_DATA_SYNCED = 'The calendars data has been synced';
    protected $client;

    public function __construct()
    {
        $client = new Google_Client();
        $client->setAuthConfig('credentials.json');
        $client->addScope(Google_Service_Calendar::CALENDAR);

        $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
        $client->setHttpClient($guzzleClient);

        $this->client = $client;
        $this->client->setAccessType("offline");
        $this->client->setApprovalPrompt("force");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $googleToken = User::select('google_session_id')->where('id',1)->first();
        if (isset($googleToken->google_session_id)) {
            $this->client->setAccessToken($googleToken->google_session_id);
            $service = new Google_Service_Calendar($this->client);
            $calendarId = 'primary';
            $results = $service->events->listEvents($calendarId);
            return $results = $results->getItems();
        } else {
            return redirect()->route('oauthCallback');
        }
    }

    public function oauth()
    {
        $rurl = action('Calendar\gCalendarController@oauth');
        $this->client->setRedirectUri($rurl);
        if (!isset($_GET['code'])) {
            $auth_url = $this->client->createAuthUrl();
            $filtered_url = filter_var($auth_url, FILTER_SANITIZE_URL);
            return redirect($filtered_url);
        } else {
            $this->client->authenticate($_GET['code']);
            $this->client->setAccessToken($this->client->getAccessToken());
            $user = User::where('id',1)->first();
            $user->google_session_id = $this->client->getAccessToken();
            $user->save();
            return redirect()->route('google.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('calendar.createEvent');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response$eventGCal
     */
    public function store(Request $request)
    {
        
        $startDateTime = $request->start_date;
        $endDateTime = $request->end_date;

        $googleToken = User::select('google_session_id')->where('id',1)->first();
        if (isset($googleToken->google_session_id)) {
            $this->client->setAccessToken($googleToken->google_session_id);
            $service = new Google_Service_Calendar($this->client);

            $calendarId = 'primary';
            $event = new Google_Service_Calendar_Event([
                'summary' => $request->title,
                'description' => $request->description,
                'start' => ['dateTime' => $startDateTime],
                'end' => ['dateTime' => $endDateTime],
                'reminders' => ['useDefault' => true],
            ]);
            $results = $service->events->insert($calendarId, $event);
            if (!$results) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
            return response()->json(['status' => 'success', 'message' => 'Event Created']);
        } 
        else {
            return redirect()->route('oauthCallback');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $eventId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */

    public function show($eventId)
    {
    
        $googleToken = User::select('google_session_id')->where('id',1)->first();
        if (isset($googleToken->google_session_id)) {
            $this->client->setAccessToken($googleToken->google_session_id);

            $service = new Google_Service_Calendar($this->client);
            $event = $service->events->get('primary', $eventId);

            if (!$event) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
            return response()->json(['status' => 'success', 'data' => $event]);

        } else {
            return redirect()->route('oauthCallback');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $eventId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(Request $request, $eventId)
    {
        $googleToken = User::select('google_session_id')->where('id',1)->first();
        if (isset($googleToken->google_session_id)) {
            $this->client->setAccessToken($googleToken->google_session_id);
            $service = new Google_Service_Calendar($this->client);

            $startDateTime = Carbon::parse($request->start_date)->toRfc3339String();

            $eventDuration = 30; //minutes

            if ($request->has('end_date')) {
                $endDateTime = Carbon::parse($request->end_date)->toRfc3339String();

            } else {
                $endDateTime = Carbon::parse($request->start_date)->addMinutes($eventDuration)->toRfc3339String();
            }

            // retrieve the event from the API.
            $event = $service->events->get('primary', $eventId);

            $event->setSummary($request->title);

            $event->setDescription($request->description);

            //start time
            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime($startDateTime);
            $event->setStart($start);

            //end time
            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime($endDateTime);
            $event->setEnd($end);

            $updatedEvent = $service->events->update('primary', $event->getId(), $event);


            if (!$updatedEvent) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
            return response()->json(['status' => 'success', 'data' => $updatedEvent]);

        } else {
            return redirect()->route('oauthCallback');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $eventId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($eventId)
    {
        $googleToken = User::select('google_session_id')->where('id',1)->first();
        if (isset($googleToken->google_session_id)){
            $this->client->setAccessToken($googleToken->google_session_id);
            $service = new Google_Service_Calendar($this->client);

            $service->events->delete('primary', $eventId);

        } else {
            return redirect()->route('oauthCallback');
        }
    }

    
    public function logout(Request $request)
    {
        $googleToken = User::where('id',1)->first();
        $this->client->setAccessToken($googleToken->google_session_id);
        $this->client->revokeToken($this->client->getAccessToken());
        $googleToken->google_session_id=null;
        $googleToken->save();
        return redirect()->route('oauthCallback');
    }


    public function showCalendars(){
   
        $googleToken = User::select('google_session_id')->where('id',1)->first();
        if (isset($googleToken->google_session_id)){
            $this->client->setAccessToken($googleToken->google_session_id);
            $service = new Google_Service_Calendar($this->client);
            $calendarList = $service->calendarList->listCalendarList()->getItems();
            return response()->json(['status' => 'success', 'data' => $calendarList]);

        } else {
            return redirect()->route('oauthCallback');
        }
        

    }

    public function syncCalendars()
    {   
        $googleToken = User::select('google_session_id')->where('id',1)->first();
        if (isset($googleToken->google_session_id)) {
            $this->client->setAccessToken($googleToken->google_session_id);
            $service = new Google_Service_Calendar($this->client);
            $calendarsList = Calendar::where('source',"Google")->get();
            $googleCalendars = $service->calendarList->listCalendarList()->getItems();
            foreach($googleCalendars as $googleCalendar){
                $exists = 0;
                foreach($calendarsList as $calendar){
                    if($googleCalendar->id == $calendar->google_calendar_id){
                        $exists = 1;
                        continue;
                    }
                }
                if($exists == 0){
                    $newCalendar = new Calendar;
                    is_null($googleCalendar->primary) ? $newCalendar->title = $googleCalendar->summary : $newCalendar->title = "Primary";
                    $newCalendar->google_calendar_id = $googleCalendar->id;                    
                    //$newCalendar->created_by = $this->userId();
                    $newCalendar->source = "Google";
                    $newCalendar->created_at = now();
                    $newCalendar->updated_at = now();
                    $newCalendar->save();
                }
            }
        } else {
            return redirect()->route('oauthCallback');
        }
        return $this->created(['message'=> gCalendarController::CALENDAR_DATA_SYNCED]);

    }

    public function syncEvents(int $id)
    {   
        $google_calendar_id = Calendar::select('google_calendar_id')->where('id',$id)->first();
        $google_calendar_id = $google_calendar_id->google_calendar_id;
        $allEvents = CalendarEvent::where('calendar_id',$id)->get();
        $googleToken = User::select('google_session_id')->where('id',1)->first();
        if (isset($googleToken->google_session_id)){
            
            $this->client->setAccessToken($googleToken->google_session_id);
            $service = new Google_Service_Calendar($this->client);
            $allEventsGCal = $service->events->listEvents($google_calendar_id)->getItems();

            foreach($allEventsGCal as $eventGCal){
                $exists = 0;
                $start = [];
                $end = [];
                is_null($eventGCal->start->dateTime) ? array_push($start,$eventGCal->start->date) : array_push($start,$eventGCal->start->dateTime);
                is_null($eventGCal->end->dateTime) ? array_push($end,$eventGCal->end->date) : array_push($end,$eventGCal->end->dateTime);
                foreach($allEvents as $event){
                    
                    if($event->g_event_id == $eventGCal->id){
                        $exists = 1;
                        $date = strtotime($eventGCal->updated);
                        $googleUpdated = date('Y/m/d H:i:s', $date);
                        $date = strtotime($event->updated_at);
                        $localUpdated = date('Y/m/d H:i:s', $date);
                        if($googleUpdated > $localUpdated){
                            $event->subject = $eventGCal->summary;
                            $date = strtotime($start[0]);
                            $event->start_date_time = date('Y/m/d H:i:s', $date);
                            $date = strtotime($end[0]);
                            $event->end_date_time = date('Y/m/d H:i:s', $date);
                            $eventGCal->visibility == "public" ? $event->visibility = "Public" : $event->visibility = "Private";
                            $eventGCal->status == "confirmed" ? $event->status = "Planned":$event->status = "Not Held";
                            $event->location = $eventGCal->location;
                            $event->description = $eventGCal->description;
                            $event->updated_at = now();
                            $event->update();
                        }
                        continue;
                    }
                }
                if($exists == 0){
                    $newEvent = new CalendarEvent;
                    $newEvent->subject = $eventGCal->summary;
                    $date = strtotime($start[0]);
                    $newEvent->start_date_time = date('Y/m/d H:i:s', $date);
                    $date = strtotime($end[0]);
                    $newEvent->end_date_time = date('Y/m/d H:i:s', $date);
                    $eventGCal->visibility == "public" ? $newEvent->visibility = "Public" : $newEvent->visibility = "Private";
                    $eventGCal->status == "confirmed" ? $newEvent->status = "Planned":$newEvent->status = "Not Held";
                    $newEvent->location = $eventGCal->location;
                    $newEvent->g_event_id = $eventGCal->id;
                    $newEvent->description = $eventGCal->description;
                    $newEvent->calendar_id = $id;
                    //$newEvent->created_by = $this->userId();
                    $newEvent->created_at = now();
                    $newEvent->updated_at = now();
                    $newEvent->save();
                    
                }
            }
        } else {
            return redirect()->route('oauthCallback');
        }
        return $this->created(['message'=> gCalendarController::EVENT_DATA_SYNCED]);

    }
}