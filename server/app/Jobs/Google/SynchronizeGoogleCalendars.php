<?php

namespace App\Jobs\Google;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\UserPr;
use App\Http\Controllers\GoogleApi\Services\GoogleCalendarService;

class SynchronizeGoogleCalendars extends SynchronizeGoogleResource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_pr;

    public function __construct(UserPr $user_pr)
    {
        $this->user_pr = $user_pr;
    }

    public function handle()
    {
        $calendar_service = $this->getGoogleService();
        $google_calendars = $this->getGoogleRequest($calendar_service);
        if($google_calendars instanceof \Google\Service\Calendar\CalendarList){
            while(true) {
                foreach ($google_calendars->getItems() as $calendarListEntry) {
                    $this->syncItem($calendarListEntry);
                }
                $pageToken = $google_calendars->getNextPageToken();
                if ($pageToken) {
                    $optParams = array('pageToken' => $pageToken);
                    $google_calendars = $calendar_service->calendarList->listCalendarList($optParams);
                    foreach ($google_calendars->getItems() as $calendarListEntry) {
                        $this->syncItem($calendarListEntry);
                    }
                } else {
                    break;
                }
            }
        }
    }

    public function getGoogleService()
    {
        return app(GoogleCalendarService::class)
            ->connectUsing($this->user_pr)
            ->service('Calendar');
    }

    public function getGoogleRequest($service)
    {
        return $service->calendarList->listCalendarList();
    }

    public function syncItem($googleCalendar)
    {
        if ($googleCalendar->deleted) {
            return $this->user_pr->googleCalendars()
                ->where('google_id', $googleCalendar->id)
                ->get()->each->delete();
        }

        $this->user_pr->googleCalendars()->updateOrCreate(
            [
                'google_calendar_id' => $googleCalendar->id,
            ],
            [
                'title' => $googleCalendar->summary,
                'color' => $googleCalendar->backgroundColor,
                'visibility' => 'Private',
                'source' => 'Default',
                'is_default' => 0,
                'timezone' => $googleCalendar->timeZone,
                'is_checked' => 1,
            ]
        );
    }
}
