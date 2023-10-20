<?php

namespace App\Http\Controllers\GoogleApi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoogleApi\Models\GoogleCalendar;
use App\Http\Controllers\GoogleApi\Models\GoogleCalenderEvent;
use App\Http\Controllers\GoogleApi\Requests\CreateEventRequest;
use App\Http\Controllers\GoogleApi\Requests\CreateGoogleCalendarRequest;
use App\Http\Controllers\GoogleApi\Requests\DeleteGoogleCalendarRequest;
use App\Http\Controllers\GoogleApi\Requests\GetAllUserEventRequest;
use App\Http\Controllers\GoogleApi\Requests\RemoveSharedCalendarWithRequest;
use App\Http\Controllers\GoogleApi\Requests\ShareCalendarRequest;
use App\Http\Controllers\GoogleApi\Requests\SharedCalendarWithRequest;
use App\Http\Controllers\GoogleApi\Requests\UpdateEventRequest;
use App\Http\Controllers\GoogleApi\Services\GoogleCalendarService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\GoogleCalendar\Event;
use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use App\Models\UserPr;
use Google_Service_Oauth2;

use function GuzzleHttp\json_decode;

class CalendarController extends Controller
{
    protected $client;

    const FETCH_ACCESS_TOKEN = 'access_token fetch successfully';
    const USER_CALENDER_LIST = "User Calender List";
    const USER_NOT_LOGGEDIN = 'User is not logged in with google';
    const CALENDAR_SHARED_SUCCESSFULLY = 'Calendar is shared successfully';
    const CALENDAR_NOT_SHARED = 'Unable to shared calendar';
    const CALENDAR_SHARED_USER_REMOVED = 'Removed shared user';

    public function __construct(GoogleCalenderEvent $model)
    {
        parent::__construct($model);
        $client = new Google_Client();
        $client->setAuthConfig(storage_path(env('GOOGLE_CALENDAR_FILE')));
        $client->setScopes(
            [
                \Google\Service\Oauth2::USERINFO_PROFILE,
                \Google\Service\Oauth2::USERINFO_EMAIL,
                \Google\Service\Oauth2::OPENID,
                Google_Service_Calendar::CALENDAR
            ]
        );
        $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
        $client->setHttpClient($guzzleClient);
        $this->client = $client;
        $this->client->setAccessType("offline");
        $this->client->setApprovalPrompt("force");
        $this->client->setIncludeGrantedScopes(true);

    }

    public function oauth(GoogleCalendarService $google_calendar_service)
    {
        $rurl = action('GoogleApi\CalendarController@oauth');
        $this->client->setRedirectUri($rurl);
        if (!isset($_GET['code'])) {
            $auth_url = $this->client->createAuthUrl();
            $filtered_url = filter_var($auth_url, FILTER_SANITIZE_URL);
            return redirect($filtered_url);
        } else {
            try {
                $this->client->authenticate($_GET['code']);
                $this->client->setAccessToken($this->client->getAccessToken());

                /**
                 * Get user's data from google
                 */
                $service = new \Google\Service\Oauth2($this->client);
                $user_from_google = $service->userinfo->get();

                $user = UserPr::where('email', $user_from_google->email)->first();
                if (is_null($user)) {
                    return response('You can only connect your Hr System email.');
                }
                $user->google_calendar_access_token = json_encode($this->client->getAccessToken());
                $user->google_calendar_refresh_token = $this->client->getAccessToken()['refresh_token'];
                $user->google_calendar_user_account_info = json_encode($user_from_google);
                $user->save();

                $google_calendar_service->syncUserCalendar($user, $this->client);
//            $google_calendar_service->syncUserCalendar($user);

                return redirect(env('GOOGLE_REDIRECT_URL'));

            } catch (Exception $ex) {
                report($ex);
                if ($ex->getCode() == 403) {
                    $message = json_decode($ex->getMessage());
                    return redirect(env('GOOGLE_REDIRECT_URL') . '/?error=' . urlencode($message->error->message));
                }

                return redirect(env('GOOGLE_REDIRECT_URL'));
            }
        }
    }

    private function refreshOauthToken()
    {
        $user_pr = UserPr::select('google_calendar_access_token')->where('id', Auth::id())->first();
        if (is_null($user_pr->google_calendar_access_token)) {
            return $this->failed(['message' => CalendarController::USER_NOT_LOGGEDIN]);
        }

        $this->client->setAccessToken($user_pr->google_calendar_access_token);
        /**
         * Handle refresh
         */
        if ($this->client->isAccessTokenExpired()) {
            // fetch new access token
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            $this->client->setAccessToken($this->client->getAccessToken());

            // save new access token
            $user_pr->google_calendar_access_token = json_encode($this->client->getAccessToken());
            $user_pr->save();
        }
    }

    public function allUserEvents(GetAllUserEventRequest $request, GoogleCalendarService $google_calendar_service)
    {
        $user_pr = UserPr::where('id', Auth::id())->first();
        if (is_null($user_pr->google_calendar_access_token)) {
            return $this->failed(['message' => CalendarController::USER_NOT_LOGGEDIN]);
        }

        try {
            $filters['calendar_id'] = $request->calendar_id;
//            $filters['start'] = $request->query('start', date('Y-m-d'));
//            $filters['end'] = $request->query('end', Carbon::createFromDate(date('Y-m-d'))->addMonths(1)->toDateString());


            $user_events = $google_calendar_service->allUserEvents($filters);


            $shared_calendars = $user_pr->sharedCalendars()->with('events')->get();
            // dd($shared_calendars);
            if($shared_calendars->isNotEmpty()){
                foreach($shared_calendars as $calendar_index => $calendar){

                    if($calendar->events->isNotEmpty()){
                        foreach($calendar->events as $event_index => $event){
                            if($calendar->pivot->role == 'freeBusyReader' && $event->transparency == 'opaque'){
                                $event['title'] = 'Busy';
                                $event['description'] = 'Busy';
                            }


                            if (!(int)$event['is_all_day']) {
                                $event['allDay'] = false;
                                $event['start'] = Carbon::createFromTimestamp(strtotime($event['start']))->toDateTimeString();
                                $event['end'] = Carbon::createFromTimestamp(strtotime($event['end']))->toDateTimeString();
                                $event['endDay'] = $event['end'];
                                $event['startDay'] = $event['start'];
                            } else {
                                $event['allDay'] = true;
                                $event['endDay'] = Carbon::createFromTimestamp(strtotime($event['end']))->addDays(-1)->toDateTimeString();
                                $event['startDay'] = $event['start'];
                            }
                            $event['eventid'] = $event['id'];
                            array_push($user_events, $event);
                        }
                    }

                }
            }

            // Update User Calendar Selection Status
            $user_pr->googleCalendars()->whereIn('id', $request->calendar_id)->update(['is_checked' => 1]);
            $user_pr->googleCalendars()->whereNotIn('id', $request->calendar_id)->update(['is_checked' => 0]);

            return $user_events;

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function createNewEvent(CreateEventRequest $request, GoogleCalendarService $calendar_service)
    {
        $this->refreshOauthToken();

        try {
            $google_calendars = GoogleCalendar::whereIn('id', $request->calendar_id)->get();
            if ($google_calendars->isNotEmpty()) {
                foreach ($google_calendars as $google_calendar) {
                    $data = [
                        'google_calendar_id' => $google_calendar->id,
                        'title' => $request->get('name'),
                        'description' => $request->get('description'),
                        'is_all_day' => $request->get('is_all_day'),
                        'start' => $request->get('start_date_time'),
                        'end' => $request->get('end_date_time'),
                        'transparency' => $request->get('transparency'),
                    ];

                    $google_service = new Google_Service_Calendar($this->client);
                    $calendar_id = $google_calendar->google_calendar_id;

                    $event = $calendar_service->createEvent($data);
                    if ($event instanceof GoogleCalenderEvent) {
                        $google_event = new Google_Service_Calendar_Event([
                            'summary' => $data['title'],
                            'description' => $data['description'],
                            'start' => ['dateTime' => Carbon::createFromTimeString($data['start'])],
                            'end' => ['dateTime' => Carbon::createFromTimeString($data['end'])],
                            'reminders' => ['useDefault' => true],
                            'transparency' => $data['transparency']
                        ]);
                        $created_google_event = $google_service->events->insert($calendar_id, $google_event);
                        if ($created_google_event instanceof \Google\Service\Calendar\Event) {
                            $event->google_event_id = $created_google_event->id;
                            $event->save();
                        }
                    }
                }
            }

            return $this->created(['message' => GoogleCalendarService::GOOGLE_CALENDAR_EVENT_CREATED, 'event' => $event]);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }

    }

    public function updateEvent(UpdateEventRequest $request, GoogleCalendarService $calendar_service)
    {
        $this->refreshOauthToken();

        try {

            $google_calendar = GoogleCalendar::find($request->calendar_id);
            $old_google_event = GoogleCalenderEvent::find($request->id);
            if (is_null($google_calendar && $old_google_event)) {
                throw new ModelNotFoundException(CalendarController::RECORD_NOT_FOUND);
            }
            $old_calendar_id = $old_google_event->calendar->google_calendar_id;

            $data = [
                'id' => $request->get('id'),
                'title' => $request->get('name'),
                'description' => $request->get('description'),
                'is_all_day' => $request->get('is_all_day'),
                'start' => $request->get('start_date_time'),
                'end' => $request->get('end_date_time'),
                'transparency' => $request->get('transparency'),
            ];

            $updated_event = $calendar_service->updateEvent($data, $google_calendar);
            if ($updated_event instanceof GoogleCalenderEvent) {
                $google_calendar_service = new Google_Service_Calendar($this->client);

                // retrieve the event from the API.
                $google_event = $google_calendar_service->events->get($updated_event->calendar->google_calendar_id, $updated_event->google_event_id);

                $google_event->setSummary($updated_event->title);
                $google_event->setDescription($updated_event->description);
                $google_event->setTransparency($updated_event->transparency);

                //start time
                $start = new Google_Service_Calendar_EventDateTime();
                $start->setDateTime(Carbon::createFromTimeString($updated_event->start));
                $google_event->setStart($start);

                //end time
                $end = new Google_Service_Calendar_EventDateTime();
                $end->setDateTime(Carbon::createFromTimeString($updated_event->end));
                $google_event->setEnd($end);


                $updated_google_event = $google_calendar_service->events->update($updated_event->calendar->google_calendar_id, $updated_event->google_event_id, $google_event);

                if ($updated_event->google_calendar_id != $google_calendar->id) {
                    $google_calendar_service->events->move($old_calendar_id, $updated_google_event->getId(), $google_calendar->google_calendar_id);
                }

                return $this->created(['message' => GoogleCalendarService::GOOGLE_CALENDAR_EVENT_UPDATED, 'event' => $updated_google_event]);
            }

            return $this->failed(['message' => GoogleCalendarService::GOOGLE_CALENDAR_EVENT_NOT_UPDATED]);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function deleteEvent(Request $request, GoogleCalendarService $calendar_service, $event_id)
    {
        $this->refreshOauthToken();
        try {
            $google_calendar_event_object = GoogleCalenderEvent::find($event_id);
            if (is_null($google_calendar_event_object)) {
                throw new ModelNotFoundException(CalendarController::RECORD_NOT_FOUND);
            }
            $google_event_id = $google_calendar_event_object->google_event_id;
            $google_calendar_id = $google_calendar_event_object->calendar->google_calendar_id;


            if ($calendar_service->deleteEvent($event_id)) {
                $google_calendar_service = new Google_Service_Calendar($this->client);

                $google_calendar_service->events->delete($google_calendar_id, $google_event_id);

                return $this->created(['message' => GoogleCalendarService::GOOGLE_CALENDAR_EVENT_DELETED]);
            } else {
                return $this->created(['message' => GoogleCalendarService::GOOGLE_CALENDAR_EVENT_NOT_DELETED]);
            }

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function shareCalendar(ShareCalendarRequest $request, GoogleCalendarService $google_calendar_service)
    {
        try {

            $this->refreshOauthToken();

            $response = $google_calendar_service->shareCalendar($this->client, $request->calendar_id, $request->user_pr_id, $request->role);
            if ($response) {
                return $this->created(['message' => CalendarController::CALENDAR_SHARED_SUCCESSFULLY]);
            }

            return $this->failed(['message' => CalendarController::CALENDAR_NOT_SHARED]);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }

    }

    public function createCalendar(CreateGoogleCalendarRequest $request, GoogleCalendarService $calendar_service)
    {
        try {
            $this->refreshOauthToken();

            $google_calendar_service = new Google_Service_Calendar($this->client);

            $google_calendar = new \Google_Service_Calendar_Calendar();
            $google_calendar->setSummary($request->title);
            $google_calendar->setTimeZone('Europe/Tallinn');

            $created_calendar = $google_calendar_service->calendars->insert($google_calendar);
            $google_calendar_object = $calendar_service->createCalendar([
                'title' => $created_calendar->getSummary(),
                'visibility' => 'Private',
                'source' => 'Default',
                'is_default' => 0,
                'google_calendar_id' => $created_calendar->getId()
            ]);

            if ($google_calendar_object instanceof GoogleCalendar) {
                return $this->created(['message' => GoogleCalendarService::GOOGLE_CALENDAR_CREATED, 'calendar' => $google_calendar_object]);
            }


        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function deleteCalendar(DeleteGoogleCalendarRequest $request, GoogleCalendarService $calendar_service)
    {
        try {
            $this->refreshOauthToken();

            $response = false;
            DB::transaction(function () use ($request, $calendar_service, &$response) {
                $google_calendar = GoogleCalendar::find($request->id);
                if (is_null($google_calendar)) {
                    throw new Exception(CalendarController::RECORD_NOT_FOUND);
                }

                $google_calendar_id = $google_calendar->google_calendar_id;

                $google_calendar_service = new Google_Service_Calendar($this->client);
                $google_calendar_service->calendars->delete($google_calendar_id);

                $google_calendar->calendarSharedWith()->detach();
                $google_calendar->events()->delete();
                $google_calendar->delete();
                $response = true;
            });

            if ($response) {
                return $this->created(['message' => GoogleCalendarService::GOOGLE_CALENDAR_DELETED]);
            } else {
                return $this->failed(['message' => GoogleCalendarService::GOOGLE_CALENDAR_NOT_DELETED]);
            }

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function getCalendars(Request $request, GoogleCalendarService $calendar_service)
    {
        try {
            $calendars = $calendar_service->getCalendars();
            if ($calendars->isEmpty()) {
                return $this->created(['message' => CalendarController::RECORD_NOT_FOUND, 'data' => []]);
            }

            return $this->created(['data' => $calendars]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function syncUserCalendar(Request $request, GoogleCalendarService $google_calendar_service)
    {
        try {
            $this->refreshOauthToken();

            $user = Auth::user();

            $google_calendar_service->syncUserCalendar($user, $this->client);
//            $google_calendar_service->syncUserCalendar($user);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }

    }

    public function logout(Request $request)
    {
        try {
            DB::transaction(function () {
                $user_pr = Auth::user();
                $this->client->setAccessToken($user_pr->google_calendar_access_token);

                $user_pr->google_calendar_access_token = null;
                $user_pr->google_calendar_refresh_token = null;
                $user_pr->google_calendar_user_account_info = null;
                $user_pr->save();

                $calendars = $user_pr->googleCalendars;
                if ($calendars->isNotEmpty()) {
                    foreach ($calendars as $calendar) {
                        $calendar->events()->delete();
                        $calendar->delete();
                    }
                }
                $user_pr->sharedCalendars()->detach();

            });

            $this->client->revokeToken($this->client->getAccessToken());
            $this->created(['message' => 'Logout out successful']);

        } catch (Exception $ex) {
            Log::info($ex);
            return $this->serverError($ex);
        }
    }

    public function webhook()
    {

    }

    public function sharedCalendarWith(SharedCalendarWithRequest $request)
    {
        try {
            $google_calendar = GoogleCalendar::with('calendarSharedWith.user')->findOrFail($request->google_calendar_id);

            $usersSharedWith = $google_calendar->calendarSharedWith;
            return $this->created(['users' => $usersSharedWith]);
        } catch (Exception $ex) {
            Log::info($ex);
            return $this->serverError($ex);
        }

    }

    public function removeSharedCalendarWith(RemoveSharedCalendarWithRequest $request, GoogleCalendarService $google_calendar_service)
    {
        $this->refreshOauthToken();

        try {
            $google_calendar_service->removeSharedCalendarWith($this->client, $request->google_calendar_id, $request->user_pr_id);

            return $this->created(['message' => CalendarController::CALENDAR_SHARED_USER_REMOVED]);
        } catch (Exception $ex) {
            Log::info($ex);
            return $this->serverError($ex);
        }

    }


}

