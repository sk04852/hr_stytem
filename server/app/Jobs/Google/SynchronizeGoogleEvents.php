<?php

namespace App\Jobs\Google;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\GoogleApi\Models\GoogleCalendar;
use App\Http\Controllers\GoogleApi\Services\GoogleCalendarService;

class SynchronizeGoogleEvents extends SynchronizeGoogleResource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $google_calendar;

    public function __construct(GoogleCalendar $google_calendar)
    {
        $this->google_calendar = $google_calendar;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $calendar_service = $this->getGoogleService();
        $google_calendar_events = $this->getGoogleRequest($calendar_service);
        while(true) {
            foreach ($google_calendar_events->getItems() as $event) {
                $this->syncItem($event);
            }
            $pageToken = $google_calendar_events->getNextPageToken();
            if ($pageToken) {
                $optParams = array('pageToken' => $pageToken);
                $google_calendar_events = $google_calendar_events->events->listEvents($this->google_calendar->google_calendar_id, $optParams);
                foreach ($google_calendar_events->getItems() as $event) {
                    $this->syncItem($event);
                }
            } else {
                break;
            }
        }
    }

    public function getGoogleService()
    {
        return app(GoogleCalendarService::class)
            ->connectUsing($this->google_calendar->userPr)
            ->service('Calendar');
    }

    public function getGoogleRequest($service)
    {
        return $service->events->listEvents(
        // We provide the Google ID of the calendar from which we want the events.
            $this->google_calendar->google_calendar_id
        );
    }

    public function syncItem($googleEvent)
    {
        // A Google event has been deleted if its status is `cancelled`.
        if ($googleEvent->status === 'cancelled') {
            return $this->google_calendar->events()
                ->where('google_event_id', $googleEvent->id)
                ->delete();
        }

        $this->google_calendar->events()->updateOrCreate(
            [
                'google_event_id' => $googleEvent->id,
            ],
            [
                'user_pr_id' => $this->google_calendar->user_pr_id,
                'start' => $this->parseDatetime($googleEvent->start),
                'end' => $this->parseDatetime($googleEvent->end),
                'status' => 1,
                'is_all_day' => $this->isAllDayEvent($googleEvent),
                'title' => $googleEvent->summary,
                'description' => $googleEvent->description,
                'visibility' => $googleEvent->visibility
            ]
        );
    }

    public function dropAllSyncedItems()
    {
        $this->synchronizable->events()->delete();
    }

    protected function isAllDayEvent($googleEvent)
    {
        return ! $googleEvent->start->dateTime && ! $googleEvent->end->dateTime;
    }

    protected function parseDatetime($googleDatetime)
    {
        $rawDatetime = $googleDatetime->dateTime ?: $googleDatetime->date;

        return Carbon::parse($rawDatetime)->setTimezone('UTC');
    }
}
