<?php

namespace App\Listeners;

use App\Events\AuditLogEvent;
use App\Models\Log\AuditLog;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AuditLogEventListener {


    protected $guard = null;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {

        /*Guard*/
        $this->guard = Config::get('auth')['customer_area_guard'];
    }

    /**
     * Handle the event.
     *
     * @param  $event AuditLogEvent
     * @return void
     */
    public function handle(AuditLogEvent $event)
    {
        /*Get the data*/
        $data = json_decode($event->data, true);
        $data['created_by'] = json_encode($this->getCreatedBy());

        /*Create sent email*/
        AuditLog::create($data);
    }

    /**
     * Returns the user details
     *
     * @return array
     */
    protected function getCreatedBy()
    {
        $array = array(
            'user_id' => null,
            'user_name' => null,
            'customer_id' => null,
            'customer_name' => null,
            'system' => false
        );

        /*Get the logged in user*/
        $user = Auth::user();

        /*Get the logged in customer*/
        $customer = Auth::guard($this->guard)->user();

        if($user != null) {

            $array['user_id'] = $user->id;
            $array['user_name'] = $user->name;

        }elseif($customer != null) {

            $array['customer_id'] = $customer->id;
            $array['customer_name'] = $customer->getFullName();

        }else{
            $array['system'] = true;
        }

        return $array;
    }

}