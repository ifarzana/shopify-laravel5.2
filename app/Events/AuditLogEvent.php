<?php

namespace App\Events;

use App\Events\Event;
//use App\Models\Error\ErrorException;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AuditLogEvent extends Event {

    use SerializesModels;

    /**
     * Data
     *
     * @var array|null
     */
    public $data = array();

    /**
     * Create a new event instance.
     *
     * @param $data array
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
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