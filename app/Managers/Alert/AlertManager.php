<?php

namespace App\Managers\Alert;

use App\Helpers\AccHelper;
use App\Managers\Alert\src\AlertFunctions;
use App\Models\Alert\Alert;
use Config;

class AlertManager extends AlertFunctions
{
    /**
     * Active alerts
     *
     * @var object
     */
    protected $alerts;

    /**
     * Construct - set all arrays and objects
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        /*Active alerts*/
        $this->alerts = Alert::where('active', 1)->orderBy('name', 'ASC')->get();
    }

    /**
     * Returns the array of active alerts
     *
     * @return array
     */
    public function getAlerts()
    {
        $has_alerts = false;
        $global_has_alerts = false;

        $array = array(
            'alerts' => array()
        );

        /*Get all the active alerts*/
        $alerts = $this->alerts;

        if(count($alerts) > 0) {

           foreach ($alerts as $alert) {

               $manager_function = $alert->manager_function;

               $result = $this->$manager_function();

               if( (count($result) > 0) OR ($alert->keep_in_alerts == 1) ) {

                   if( ($alert->keep_in_alerts == 1) AND (count($result) == 0) ) {
                       $has_alerts = false;
                   }

                   if(count($result) > 0) {
                       $has_alerts = true;
                       $global_has_alerts = true;
                   }

                   $array['alerts'][$alert->category->name][] = array(
                       'name' => $alert->name,
                       'count' => count($result),
                       'key' => $alert->key
                   );
               }

           }

        }

        if($global_has_alerts != $has_alerts) {
            $has_alerts = true;
        }

        $array['has_alerts'] = $has_alerts;

        if($has_alerts == true) {
           /*Sort by key*/
           ksort($array['alerts']);
        }

        return $array;
    }

    /**
     * Returns the alert results by key - controller function
     *
     * @param $key string
     * @return false|array
     */
    public function getResultsByKey($key)
    {
        if( ($key == null) OR (empty($key)) ) {
            return false;
        }

        /*Get the alert*/
        $Alert = Alert::where('key', $key)->first();

        $function = $Alert->controller_function;

        $results = $this->$function();

        $array = array(
            'alert' => $Alert,
            'results' => $results
        );

        return $array;
    }


}