<?php

namespace App\Models\Editor;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CacheQueryBuilderTrait;

class EditorPlaceholder extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'CONF_editor_placeholder';

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
    protected $fillable = ['display', 'field', 'function', 'exclude_mk', 'exclude_ca'];

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

}