<?php

namespace App\Models\Alert;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\CacheQueryBuilderTrait;
use Config;

class Alert extends Model
{
    use SearchableTrait;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'AL_alert';

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
    protected $fillable = ['name', 'description', 'category_id', 'key', 'manager_function', 'controller_function', 'keep_in_alerts', 'active'];

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
        'name' => 'required|max:45',
        'description' => 'required|max:255',
        'category_id' => 'required|integer',
        'key' => 'required|max:45',
        'manager_function' => 'required|max:255',
        'controller_function' => 'required|max:255',
        'keep_in_alerts' => 'required|boolean',
        'active' => 'required|boolean'
    );
    
    /**
     * The custom messages used for the validation
     *
     * @var array
     */
    public static $messages = array(
        //
    );
    
    public function category()
    {
        return $this->hasOne(AlertCategory::class, 'id', 'category_id');
    }

    /**
     * 'Fetch all' function
     *
     * @param $query Builder
     * @param $paginationData array
     * @param $search_by string
     * @param $where array
     * @param $whereIn array
     * @return object
     */
    public function scopeFetchAll(Builder $query, $paginationData = array(), $search_by, $where = array(), $whereIn = array())
    {
        /*BEGIN SEARCH*/
        if($search_by != null) {
            $results = $this::search(urldecode($search_by));
        }else{
            $results = $this;
        }
        /*END SEARCH*/

        /*BEGIN WHERE*/
        if(!empty($where)) {
            $results = $results->where($where);
        }
        /*END WHERE*/

        /*BEGIN WHERE IN*/
        if(!empty($whereIn)) {
            $results = $results->whereIn('AL_alert.id', $whereIn);
        }
        /*END WHERE IN*/
        
        /*BEGIN PAGINATION*/
        $paginated = isset($paginationData['paginated']) ? $paginationData['paginated']: false;

        if($paginated) {

            $paginationConfig = Config::get('pagination.pagination');

            $order_by         = $this->getOrderBy($paginationData);
            $order            = $paginationData['order'];
            $p                = $paginationData['p'];
            $itemCountPerPage = isset($paginationData['per_page']) ? $paginationData['per_page'] : $paginationConfig['itemCountPerPage'];

            $results = $results->orderBy($order_by, $order)->paginate($itemCountPerPage, ['*'], 'p');

            return $results;

        }else {
            if( (isset($paginationData['order_by'])) && (isset($paginationData['order'])) ) {
                $results = $results->orderBy($this->getOrderBy($paginationData), $paginationData['order']);
            }else{
                $results = $results->orderBy($this->default_order_by, $this->default_order);
            }
        }

        /*END PAGINATION*/

        return $results->get();
    }


}