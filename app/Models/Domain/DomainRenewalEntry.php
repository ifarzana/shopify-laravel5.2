<?php

namespace App\Models\Domain;

use App\Models\Invoice\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Config;

class DomainRenewalEntry extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'WH_domain_renewal_entry';

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
    protected $fillable = ['domain_id', 'renewal_date', 'cost',
        'interval_id', 'renewal_period', 'expiry_date', 'status_id', 'added_by_user_name'];

    /**
     * The rules used for the validation
     *
     * @var array
     */
    public static $rules = array(
        'domain_id' => 'required|integer',

        'renewal_date' => 'required|date',
        'renewal_period' => 'required|integer|min:1',

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
    public function domain()
    {
        return $this->hasOne(Domain::class, 'id', 'domain_id');
    }

    public function interval()
    {
        return $this->hasOne(Interval::class, 'id', 'interval_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'id', 'invoice_id');
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

    /**
     * Return the object by invoice id
     *
     * @param $invoice_id integer
     *
     * @return object
     */
    public static function findByInvoiceId($invoice_id)
    {
        $result = DomainRenewalEntry::where('invoice_id', (int)$invoice_id)->first();
        return $result;
    }



}