<?php
/**
 * GoogleCalendarService
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\GoogleApi\Services;

use App\Http\Controllers\GoogleApi\CalendarController;
use App\Http\Controllers\GoogleApi\Models\GoogleCalendar;
use App\Http\Controllers\GoogleApi\Models\GoogleCalenderEvent;
use App\Jobs\Google\SynchronizeGoogleCalendars;
use App\Models\UserPr;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_AclRule;
use Google_Service_Calendar_AclRuleScope;
use Illuminate\Support\Facades\DB;

class GoogleCalendarService
{
    protected $client;

    const GOOGLE_CALENDAR_CREATED = 'Google Calendar is successfully created';
    const GOOGLE_CALENDAR_NOT_CREATED = 'Unable to create Google Calendar';
    const GOOGLE_CALENDAR_DELETED = 'Google Calendar is successfully delete';
    const GOOGLE_CALENDAR_NOT_DELETED = 'Unable to delete Google Calendar';
    const GOOGLE_CALENDAR_EVENT_CREATED = 'Google Calendar Event is successfully created';
    const GOOGLE_CALENDAR_EVENT_NOT_CREATED = 'Unable to create Google Calendar Event';
    const GOOGLE_CALENDAR_EVENT_UPDATED = 'Google Calendar Event is successfully updated';
    const GOOGLE_CALENDAR_EVENT_NOT_UPDATED = 'Unable to update Google Calendar Event';
    const GOOGLE_CALENDAR_EVENT_DELETED = 'Google Calendar Event is successfully deleted';
    const GOOGLE_CALENDAR_EVENT_NOT_DELETED = 'Unable to delete Google Calendar Event';
    const GOOGLE_CALENDAR_EVENT_NOT_FOUND = 'Google Calendar Event not found';

    function __construct()
    {
        $client = new Google_Client();
        $client->setClientId(config('google-service.google.client_id'));
        $client->setClientSecret(config('google-service.google.client_secret'));
        $client->setRedirectUri(config('google-service.google.redirect_uri'));
        $client->setScopes(config('google-service.google.scopes'));
        $client->setApprovalPrompt(config('google-service.google.approval_prompt'));
        $client->setAccessType(config('google-service.google.access_type'));
        $client->setIncludeGrantedScopes(config('google-service.google.include_granted_scopes'));
        $this->client = $client;
    }

    public function __call($method, $args)
    {
        if (! method_exists($this->client, $method)) {
            throw new \Exception("Call to undefined method '{$method}'");
        }

        return call_user_func_array([$this->client, $method], $args);
    }

    public function service($service)
    {
        $classname = "Google_Service_$service";

        return new $classname($this->client);
    }

    public function connectUsing($user_pr)
    {
        $this->client->setAccessToken($user_pr->google_calendar_access_token);

        if ($this->client->isAccessTokenExpired()) {
            // fetch new access token
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            $this->client->setAccessToken($this->client->getAccessToken());

            // save new access token
            $user_pr->google_calendar_access_token = json_encode($this->client->getAccessToken());
            $user_pr->save();
        }

        return $this;
    }

    public function allUserEvents($filters)
    {
        $data = collect([]);
        $google_calendar_event_query = GoogleCalenderEvent::query();
        $google_calendar_event_query->where('user_pr_id', '=', Auth::id());
        $google_calendar_event_query->whereIn('google_calendar_id', $filters['calendar_id']);
        $google_calendar_event_query->with('calendar');

//        if ($filters['start']) {
//            $google_calendar_event_query->where('start', '>=', $filters['start']);
//        }
//        if ($filters['end']) {
//            $google_calendar_event_query->where('end', '<=', $filters['end']);
//        }

        $user_events = $google_calendar_event_query->get();
        if ($user_events->isNotEmpty()) {
            foreach ($user_events as $event) {
                if (!(int)$event['is_all_day']) {
                    $event['allDay'] = false;
                    $event['start'] = Carbon::createFromTimestamp(strtotime($event['start']))->toDateTimeString();
                    $event['end'] = Carbon::createFromTimestamp(strtotime($event['end']))->toDateTimeString();
                    $event['endDay'] = $event['end'];
                    $event['startDay'] = $event['start'];
//                    $event['date'] = $event['start'];
                } else {
                    $event['allDay'] = true;
                    $event['endDay'] = Carbon::createFromTimestamp(strtotime($event['end']))->addDays(-1)->toDateTimeString();
                    $event['startDay'] = $event['start'];
//                    $event['date'] = Carbon::createFromTimestamp(strtotime($event['start']))->toDateString();
                }
                $event['eventid'] = $event['id'];
                $data->push($event);
            }
        }
        return $data->toArray();
    }

    public function createEvent($data)
    {

        //When Event is all day then we need to add the 1 day to complete the last day
//        if(isset($data['is_all_day']) && $data['is_all_day'] == 1){
//            $date_diff = Carbon::createFromTimestamp(strtotime($data['end']))->diffInDays(Carbon::createFromTimestamp(strtotime($data['start'])));
//            $data['end'] = Carbon::createFromTimestamp(strtotime($data['start']))->addDays($date_diff)->toDateString();
//        }


        $data['user_pr_id'] = Auth::id();
        $google_calendar_event_object = GoogleCalenderEvent::create($data);
        if (!$google_calendar_event_object instanceof GoogleCalenderEvent) {
            throw new Exception(self::GOOGLE_CALENDAR_EVENT_NOT_CREATED);
        }

        return $google_calendar_event_object;

    }

    public function updateEvent($data, $google_calendar) {
        $id =  $data['id'];
        unset($data['id']);
        $google_calendar_event_object = GoogleCalenderEvent::find($id);
        if(is_null($google_calendar_event_object)){
            throw new Exception(self::GOOGLE_CALENDAR_EVENT_NOT_FOUND);
        }

        if($google_calendar_event_object->google_calendar_id != $google_calendar->id){
            $data['google_calendar_id'] = $google_calendar->id;
        }

        $google_calendar_event_object->fill($data);
        if($google_calendar_event_object->save()){
            return $google_calendar_event_object;
        }else{
            throw new Exception(self::GOOGLE_CALENDAR_EVENT_NOT_UPDATED);
        }

    }


    public function deleteEvent($event_id){
        $google_calendar_event_object = GoogleCalenderEvent::find($event_id);
        if(is_null($google_calendar_event_object)){
            throw new Exception(self::GOOGLE_CALENDAR_EVENT_NOT_FOUND);
        }

        return $google_calendar_event_object->delete();
    }

    public function createCalendar($data){
        $data['user_pr_id'] = Auth::id();
        $google_calendar_object = GoogleCalendar::create($data);
        if (!$google_calendar_object instanceof GoogleCalendar) {
            throw new Exception(self::GOOGLE_CALENDAR_NOT_CREATED);
        }

        return $google_calendar_object;
    }

    public function getCalendars(){
        $user_pr = Auth::user();

        $calendars = collect([]);
        $calendars->put('personal', $user_pr->googleCalendars);
        $calendars->put('other', $user_pr->sharedCalendars);


        return $calendars;
    }

//    public function syncUserCalendar($user){
//        SynchronizeGoogleCalendars::dispatch($user);
//    }

    public function syncUserCalendar($user, $google_client){
        DB::transaction(function () use ($user, $google_client){
            $google_calendar_service = new Google_Service_Calendar($google_client);
            $calendarList = $google_calendar_service->calendarList->listCalendarList();
            foreach ($calendarList->getItems() as $calendarListEntry) {
                if($calendarListEntry->getId() == $user->email && !$calendarListEntry->deleted){
                    $temp_calendar = $user->googleCalendars()->create([
                        'title' => $calendarListEntry->getSummary(),
                        'visibility'=> 'Private',
                        'source'=> 'Default',
                        'is_default'=> 1,
                        'google_calendar_id' => $calendarListEntry->getId()
                    ]);

                    $this->syncEvents($temp_calendar, $google_calendar_service);

                }else{
                    if(!$calendarListEntry->deleted){
                        $temp_calendar = $user->googleCalendars()->create([
                            'title' => $calendarListEntry->getSummary(),
                            'visibility'=> 'Private',
                            'source'=> 'Default',
                            'is_default'=> 0,
                            'google_calendar_id' => $calendarListEntry->getId()
                        ]);

                        $this->syncEvents($temp_calendar, $google_calendar_service);
                    }
                }
            }
        });
    }

    public function syncEvents($calendar, $google_calendar_service){

        DB::transaction(function() use ($calendar, $google_calendar_service){
            $events = $google_calendar_service->events->listEvents($calendar->google_calendar_id);

            while(true) {
                foreach ($events->getItems() as $event) {
                    if(!is_null($event->getStart()) && $event->status !== 'cancelled'){
                        $start = (is_null($event->getStart()->getDateTime())) ? $event->getStart()->getDate() : $event->getStart()->getDateTime();
                        $end = (is_null($event->getEnd()->getDateTime())) ? $event->getEnd()->getDate() : $event->getEnd()->getDateTime();

                        GoogleCalenderEvent::create([
                            'google_calendar_id' => $calendar->id,
                            'user_pr_id' => $calendar->user_pr_id,
                            'start' => Carbon::parse($start)->format('Y-m-d H:i:s'),
                            'end' => Carbon::parse($end)->format('Y-m-d H:i:s'),
                            'status' => 1,
                            'is_all_day' => null,
                            'title' => $event->getSummary(),
                            'description' => $event->getDescription(),
                            'google_event_id' => $event->getId(),
                            'visibility' => $event->getVisibility(),
                            'transparency' => $event->getTransparency()
                        ]);
                    }
                }
                $pageToken = $events->getNextPageToken();
                if ($pageToken) {
                    $optParams = array('pageToken' => $pageToken);
                    $events = $google_calendar_service->events->listEvents($calendar->google_calendar_id, $optParams);
                    foreach ($events->getItems() as $event) {
                        if(!is_null($event->getStart()) && $event->status !== 'cancelled'){
                            $start = (is_null($event->getStart()->getDateTime())) ? $event->getStart()->getDate() : $event->getStart()->getDateTime();
                            $end = (is_null($event->getEnd()->getDateTime())) ? $event->getEnd()->getDate() : $event->getEnd()->getDateTime();

                            GoogleCalenderEvent::create([
                                'google_calendar_id' => $calendar->id,
                                'user_pr_id' => $calendar->user_pr_id,
                                'start' => Carbon::parse($start)->format('Y-m-d H:i:s'),
                                'end' => Carbon::parse($end)->format('Y-m-d H:i:s'),
                                'status' => 1,
                                'is_all_day' => null,
                                'title' => $event->getSummary(),
                                'description' => $event->getDescription(),
                                'google_event_id' => $event->getId(),
                                'visibility' => $event->getVisibility(),
                                'transparency' => $event->getTransparency()
                            ]);
                        }
                    }
                } else {
                    break;
                }
            }
        });

        return;
    }

    public function shareCalendar($google_client, $calendar_id, $user_pr_id, $role){

        $google_calendar_object = GoogleCalendar::find($calendar_id);
        $invited_by = Auth::id();
        if(is_null($google_calendar_object)){
            throw new ModelNotFoundException('Calendar not found');
        }

        if(is_array($user_pr_id) && !empty($user_pr_id)){
            foreach ($user_pr_id as $id){
                $user_pr_object = UserPr::find($id);
                if(is_null($user_pr_object)){
                    throw new ModelNotFoundException('Recruiter not found');
                }

                $google_calendar_service = new Google_Service_Calendar($google_client);

                $rule = new Google_Service_Calendar_AclRule();
                $scope = new Google_Service_Calendar_AclRuleScope();

                $scope->setType("user");
                $scope->setValue($user_pr_object->email);
                $rule->setScope($scope);
                $rule->setRole($role);


                $createdRule = $google_calendar_service->acl->insert($google_calendar_object->google_calendar_id, $rule);

                $user_pr_object->sharedCalendars()->attach($calendar_id, [
                    'invited_by_id' => $invited_by,
                    'rule_id' => $createdRule->id,
                    'role' => $role
                ]);

                return true;

            }

        }
    }

    public function removeSharedCalendarWith($google_client, $google_calendar_id, $user_pr_id){

        $google_calendar = GoogleCalendar::findOrFail($google_calendar_id);
        $user_pr = UserPr::findOrFail($user_pr_id);

        $google_calendar_service = new Google_Service_Calendar($google_client);

        $userSharedWith = $google_calendar->calendarSharedWith()->where('user_pr_id', $user_pr->id)->firstOrFail();
        $google_calendar_service->acl->delete($google_calendar->google_calendar_id, $userSharedWith->pivot->rule_id);
        $google_calendar->calendarSharedWith()->detach($user_pr);

    }

}
