<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SearchableTrait;
use Config;

class Group extends Model
{
    use SearchableTrait;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'UM_user_group';

    /**
     * The default order by
     *
     * @var string
     */
    protected $default_order_by = 'name';

    /**
     * The default order column
     *
     * @var string
     */
    protected $default_order = 'ASC';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

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
    protected $fillable = ['name', 'description'];

    /**
     * The joins used by the model.
     *
     * @var array
     */
    protected $joins = array(
        //
    );

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
        'name' => 'unique:UM_user_group,name|required|max:45',
        'description' => 'max:45',
    );

    /**
     * The custom messages used for the validation
     *
     * @var array
     */
    public static $messages = array(
        //
    );

    public function users()
    {
        return $this->belongsToMany(User::class, 'id', 'group_id');
    }

    /**
     * 'Fetch all' function
     *
     * @param $query Builder
     * @param $paginationData array
     * @param $search_by string
     * @param $where array
     * @return object
     */
    public function scopefetchAll(Builder $query, $paginationData = array(), $search_by, $where = array())
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
        
        /*BEGIN PAGINATION*/
        $paginated = isset($paginationData['paginated']) ? $paginationData['paginated']: false;

        if($paginated) {

            $paginationConfig = Config::get('pagination.pagination');

            $order_by         = $this->getOrderBy($paginationData);
            $order            = $paginationData['order'];
            $p                = $paginationData['p'];
            $itemCountPerPage = $paginationConfig['itemCountPerPage'];

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