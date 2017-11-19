<?php

namespace App\Traits;

use App\Events\AuditLogEvent;
use Illuminate\Support\Facades\Event;

trait AuditLogTrait
{
    /**
     * Original data
     *
     * @var array
     */
    private $originalData = array();

    /**
     * Updated data
     *
     * @var array
     */
    private $updatedData = array();

    /**
     * Just created key
     *
     * @var null
     */
    protected $just_created_key = null;

    /**
     * Boot function
     *
     * @return void
     */
    public static function boot()
    {
        if(getenv('APP_ENV') == 'production') {
            static::bootAuditLogTrait();
        }
    }

    /**
     * Internal boot function
     *
     * @return void
     */
    public static function bootAuditLogTrait()
    {
        static::saving(function ($model) {
            $model->preSave();
        });

        static::saved(function ($model) {
            $model->postSave();
        });

        static::created(function($model){
            $model->postCreate();
        });

        static::deleted(function ($model) {
            $model->preSave();
            $model->postDelete();
        });
    }

    /**
     * Save event
     *
     * @return bool
     */
    public function preSave()
    {
        if (!isset($this->auditLogEnabled) || $this->auditLogEnabled == true) {

            $this->originalData = $this->original;
            $this->updatedData = $this->attributes;

            return true;
        }

        return false;
    }

    /**
     * Update event
     *
     * @return bool
     */
    public function postSave()
    {
        if ((!isset($this->auditLogEnabled) || $this->auditLogEnabled == true))
        {

            if($this->just_created_key != $this->getKey()) {

                if($this->isDifferent() == true) {

                    $array = $this->buildData('update');

                    /*Fire event*/
                    Event::fire(new AuditLogEvent(json_encode($array)));

                    return true;
                }

                return false;

            }else{
                $this->just_created_key = null;
            }

            return true;

        }

        return false;
    }

    /**
     * Create event
     *
     * @return bool
     */
    public function postCreate()
    {
        if ((!isset($this->auditLogEnabled) || $this->auditLogEnabled == true))
        {
            $array = $this->buildData('create');

            /*Fire event*/
            Event::fire(new AuditLogEvent(json_encode($array)));

            $this->just_created_key = $this->getKey();

            return true;
        }

        return false;
    }

    /**
     * Delete event
     *
     * @return bool
     */
    public function postDelete()
    {
        if ((!isset($this->auditLogEnabled) || $this->auditLogEnabled == true))
        {
            $array = $this->buildData('delete');

            /*Fire event*/
            Event::fire(new AuditLogEvent(json_encode($array)));

            return true;
        }

        return false;
    }

    /**
     * Build the array to be sent
     *
     * @param $type string
     * @return array
     */
    protected function buildData($type)
    {
        $array = array(
            'object' => $this->getMorphClass(),
            'object_id' => $this->getKey(),
            'type' => $type,
            'data' => json_encode(array(
                'original_data' => $this->originalData,
                'updated_data' => $this->updatedData
            ))
        );

        return $array;
    }

    /**
     * Returns true if the object is different
     *
     * @return bool
     */
    protected function isDifferent()
    {
        $diff1 = array_diff_assoc($this->originalData, $this->updatedData);
        $diff2 = array_diff_assoc($this->updatedData, $this->originalData);

        return ( (!empty($diff1)) OR (!empty($diff2)) ) ? true : false;
    }

}
