<?php

namespace App\Models\Hosting;

use App\Models\Client\Client;
use App\Models\Client\ClientContact;
use App\Models\Currency\Currency;
use App\Models\Domain\DomainStatus;
use App\Models\Domain\Interval;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SearchableTrait;
use Config;
use Illuminate\Support\Facades\DB;

class Hosting extends Model
{
    use SearchableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'WH_hosting';

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
    protected $fillable = ['name', 'client_id', 'contact_id', 'registration_date', 'registration_period', 'currency_id', 'cost',
        'interval_id', 'expiry_date', 'status_id'];

    /**
     * The rules used for the validation
     *
     * @var array
     */
    public static $rules = array(
        'name' => 'required',
        'client_id' => 'required|integer',
        'contact_id' => 'integer',

        'registration_date' => 'required|date',
        'registration_period' => 'required|integer|min:1',

        'expiry_date' => 'date',
        'interval_id' => 'required',
        'status_id' => 'required',
        'cost' => 'required|numeric|min:0',
    );

    /**
     * The custom messages used for the validation
     *
     * @var array
     */
    public static $messages = array(
    //
    );

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function contact()
    {
        return $this->hasOne(ClientContact::class, 'id', 'contact_id');
    }

    public function interval()
    {
        return $this->hasOne(Interval::class, 'id', 'interval_id');
    }

    public function status()
    {
        return $this->hasOne(DomainStatus::class, 'id', 'status_id');
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
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