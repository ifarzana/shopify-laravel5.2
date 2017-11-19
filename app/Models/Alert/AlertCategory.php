<?php

namespace App\Models\Alert;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CacheQueryBuilderTrait;

class AlertCategory extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'AL_alert_category';

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
    protected $fillable = ['name'];
    
}