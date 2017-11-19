<?php

namespace App\Listeners;

use App\Events\ErrorExceptionCreatedEvent;
use App\Managers\System\SystemManager;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\App;

class ErrorExceptionCreatedEventListener {

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
     * @param  $event ErrorExceptionCreatedEvent
     * @return void
     */
    public function handle(ErrorExceptionCreatedEvent $event)
    {
        //$ErrorException = $event->errorException;

        //$systemManager = App::make(SystemManager::class);

        //$systemManager->sendExceptionEmails($ErrorException);
    }


}