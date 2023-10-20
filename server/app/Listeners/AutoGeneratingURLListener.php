<?php

namespace App\Listeners;

use App\Events\InvalidateUrl;

class AutoGeneratingURLListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Create Event Listener.
     *
     * @param  object  $event
     * @return void
     */
    public function handleInvalidateURL($event)
    {
        $event->auto_generated_url->status = 'invalid';
        $event->auto_generated_url->save();
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function subscribe($events){

        $events->listen(
            InvalidateUrl::class,
            'App\Listeners\AutoGeneratingURLListener@handleInvalidateURL'
        );

    }
}
