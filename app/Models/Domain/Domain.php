<?php

namespace App\Models\Domain;

use App\Models\Client\Client;
use App\Models\Client\ClientContact;
use App\Models\Currency\Currency;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SearchableTrait;
use Config;
use Illuminate\Support\Facades\Auth;

class Domain extends Model
{
    use SearchableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'WH_domain';

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
        0 => array(
            'type' => 'inner',
            'table' => 'CL_client',
            'rel' => array(
                'one' => 'WH_domain.client_id',
                'two' => 'CL_client.id'
            ),
            'columns' => array(
                'name' => 'client_name',
            ),
        ),
    );

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'registrar_id', 'server_id', 'cms', 'ssl', 'client_id', 'contact_id', 'registration_date', 'currency_id', 'cost',
        'interval_id', 'registration_period', 'expiry_date', 'status_id'];

    /**
     * The rules used for the validation
     *
     * @var array
     */
    public static $rules = array(
        'name' => 'required',
        'registrar_id' => 'integer',
        'server_id' => 'integer',
        'cms' => 'max:45',
        'ssl' => 'max:45',
        'client_id' => 'required|integer',

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

    public function server()
    {
        return $this->hasOne(Server::class, 'id', 'server_id');
    }

    public function registrar()
    {
        return $this->hasOne(Registrar::class, 'id', 'registrar_id');
    }

    /**
     * Fetch all function
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
     * Returns the all the domains of an user
     *
     * @param $user_id int
     *
     * @return object
     */
    public static function getUserDomains($user_id)
    {
        $columns = array(
            'WH_domain.*',
        );

        $results = Domain::select($columns)
            ->orderBy('WH_domain.name', 'ASC')
            ->join('WH_assigned_domain','WH_assigned_domain.domain_id', '=', 'WH_domain.id' , 'left')
            ->where('WH_assigned_domain.user_id', '=', (int)$user_id)
            ->get();

        return $results;
    }

    /**
     * Returns the user
     *
     * @param $user_id int
     *
     * @return object
     */
    public static function getUser($domain_id)
    {

        $columns = array(
            'UM_user.*'
        );

        $result = User::select($columns)
            ->join('WH_assigned_domain','UM_user.id', '=', 'WH_assigned_domain.user_id' , 'left')
            ->where('WH_assigned_domain.domain_id', '=', (int)$domain_id)
            ->first();

        return $result;
    }

    /**
     * Returns those domains which are due in a date (from_date) for the reminder email
     *
     * @param $from_date string
     * @return array
     */
    public static function getDueDomainForReminder($from_date)
    {
        /*Get all the domains*/
        $domains = Domain::all();

        $domain_array = array();
        /*Get renewal entries*/
        foreach ($domains as $domain){
            $expiry_date = DomainRenewalEntry::where('domain_id', $domain->id)->max('expiry_date');

            if($expiry_date == NULL){
                $expiry_date = $domain->expiry_date;
            }
            else {
            }

            if($expiry_date == $from_date) {
                $domain_array[$domain->id] = true;
            }
        }

        return $domain_array;
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
        $result = Domain::where('invoice_id', (int)$invoice_id)->first();
        return $result;
    }

    /**
     * Return the domains of user registered on current date
     *
     * @param $date string
     *
     * @return object
     */
    public static function getDateDomains($date)
    {
        $user_id = Auth::user()->id;

        $columns = array(
            'WH_domain.*',
        );

        $results = Domain::select($columns)
            ->where('WH_domain.registration_date', '=', $date)
            ->join('WH_assigned_domain','WH_assigned_domain.domain_id', '=', 'WH_domain.id' , 'left')
            ->where('WH_assigned_domain.user_id', '=', (int)$user_id)
            ->get();

        return $results;

    }

    /**
     * Return the domains expiring in next 30 days
     *
     * @param $date string
     *
     * @return object
     */
    public static function getDateDomainsExpiringInThirtyDays($date)
    {
        $user_id = Auth::user()->id;

        $columns = array(
            'WH_domain.*',
        );

        $results = Domain::select($columns)
            ->where('WH_domain.expiry_date', '<=', $date)
            ->join('WH_assigned_domain','WH_assigned_domain.domain_id', '=', 'WH_domain.id' , 'left')
            ->where('WH_assigned_domain.user_id', '=', (int)$user_id)
            ->get();

        return $results;

    }

}