<?php

namespace App\Models\Country;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\CacheQueryBuilderTrait;

class Country extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'CONF_country';

    /**
     * The default order by
     *
     * @var string
     */
    protected $default_order_by = 'order';

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'code', 'order'];
    
    public function scopeGetAllCountries(Builder $query)
    {
        $query->orderBy($this->default_order_by, $this->default_order);
        
        return $query->get();
    }
    
}