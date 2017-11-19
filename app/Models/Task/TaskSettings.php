<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Model;

class TaskSettings extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'TSK_task_settings';

    /**
     * The group by column
     *
     * @var string
     */
    protected $group_by = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['schedule_maximum_days_range', 'schedule_refresh_interval', 'schedule_enable_drag_and_drop',
        'schedule_enable_context_menu', 'schedule_context_menu_items', 'schedule_weekend_days', 'schedule_business_hours_from_time', 'schedule_business_hours_to_time'];

    /**
     * The rules used for the validation
     *
     * @var array
     */
    public static $rules = array(
        'schedule_maximum_days_range' => 'required|integer',
        'schedule_refresh_interval' => 'required|integer|min:60|max:86400',
        'schedule_enable_drag_and_drop' => 'required|boolean',
        'schedule_enable_context_menu' => 'required|boolean',
    );

    /**
     * The custom messages used for the validation
     *
     * @var array
     */
    public static $messages = array(
        //
    );

    /**
     * Returns the task settings object
     *
     * @return object
     */
    public static function getSettings()
    {
        $result = TaskSettings::find(1);
        return $result;
    }
}
