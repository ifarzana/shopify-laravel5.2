<?php

namespace App\Models\Currency;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CacheQueryBuilderTrait;

class Currency extends Model
{
    /**
     * For Caching all Queries.
     */
    use CacheQueryBuilderTrait;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'CONF_currency';

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
    protected $fillable = ['name', 'symbol', 'default'];

    /**
     * Returns the active currency object
     *
     * @return  object
     */
    public static function getActiveCurrency()
    {
        $result = Currency::where('default', '1')->first();
        return $result;
    }
    
}