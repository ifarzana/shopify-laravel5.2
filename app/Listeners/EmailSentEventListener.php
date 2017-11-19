<?php

namespace App\Listeners;

use App\Events\EmailSentEvent;
use App\Models\Email\SentEmail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailSentEventListener {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  $event EmailSentEvent
     * @return void
     */
    public function handle(EmailSentEvent $event)
    {
        /*Get the data*/
        $data = $event->data;

        /*Create sent email*/
        SentEmail::create(array(
            'data' => $data
        ));
    }


}