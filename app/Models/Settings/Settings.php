<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use Config;

class Settings extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'CONF_global_settings';

    /**
     * The default order by
     *
     * @var string
     */
    protected $default_order_by = 'id';

    /**
     * The default order column
     *
     * @var string
     */
    protected $default_order = 'ASC';

    /**
     * The group by column
     *
     * @var string
     */
    protected $group_by = null;

    /**
     * The joins used by the model.
     *
     * @var array
     */
    protected $joins = array(
        //
    );

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        //
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The rules used for the validation
     *
     * @var array
     */
    public static $rules = array(
        //
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
     * Returns the settings object
     *
     * @return object
     */
    public static function getSettings()
    {
        $result = Settings::find(1);
        return $result;
    }

}