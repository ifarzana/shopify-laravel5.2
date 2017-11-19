<?php

namespace App\Models\Domain;

use Illuminate\Database\Eloquent\Model;

class DomainSettings extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'WH_domain_settings';

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
    protected $fillable = ['days_before_renewal_due_reminder'];

    /**
     * The rules used for the validation
     *
     * @var array
     */
    public static $rules = array(
        'days_before_renewal_due_reminder' => 'required|integer'
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
    public static function getDomainSettings()
    {
        $result = DomainSettings::find(1);
        return $result;
    }
}
