<?php

namespace App\Models\Domain;

use App\Models\Client\Client;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SearchableTrait;
use Config;

class AssignedDomain extends Model
{
    use SearchableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'WH_assigned_domain';

    /**
     * The default order by
     *
     * @var string
     */
    protected $default_order_by = 'created_at';

    /**
     * The default order column
     *
     * @var string
     */
    protected $default_order = 'DESC';

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
    protected $fillable = ['user_id', 'domain_id'];

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

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function domain()
    {
        return $this->hasOne(Domain::class, 'id', 'domain_id');
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