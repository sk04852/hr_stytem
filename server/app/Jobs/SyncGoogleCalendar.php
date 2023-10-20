<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\UserPr;
use App\Http\Controllers\GoogleApi\Services\GoogleCalendarService;

class SyncGoogleCalendar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The UserPr instance.
     *
     * @var UserPr
     */
    protected $user_pr;

    /**
     * Create a new UserPr instance.
     *
     * @param  UserPr  $user_pr
     * @return void
     */
    public function __construct(UserPr $user_pr)
    {
        $this->user_pr = $user_pr->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @param  GoogleCalendarService  $google_calendar_service
     * @return void
     */
    public function handle(GoogleCalendarService $google_calendar_service)
    {
        $google_calendar_service->syncUserCalendar($this->user_pr);
    }
}
