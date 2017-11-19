<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Error\ErrorException;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ErrorExceptionCreatedEvent extends Event {

    use SerializesModels;

    /**
     * Error exception
     *
     * @var object|null
     */
    public $errorException = null;

    /**
     * Create a new event instance.
     *
     * @param $errorException ErrorException
     * @return void
     */
    public function __construct(ErrorException $errorException)
    {
        $this->errorException = $errorException;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}