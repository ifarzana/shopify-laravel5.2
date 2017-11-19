<?php

namespace App\Models\Invoice;

use App\Models\Domain\Domain;
use App\Models\Currency\Currency;
use App\Models\Client\Client;
use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SearchableTrait;
use Config;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{

    use SearchableTrait;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'INV_invoice';

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
    protected $default_order = 'DESC';

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
    protected $fillable = ['currency_id', 'user_id', 'domain_id', 'reference', 'client_id', 'type_id', 'total', 'fully_refunded', 'sent', 'sent_at', 'email_sent', 'status_id', 'filename', 'created_at', 'updated_at'];

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
        'currency_id' => 'required|integer',
        'user_id' => 'required|integer',
        'domain_id' => 'integer',
        'reference' => 'required',
        'client_id' => 'required|integer',
        'type_id' => 'required|integer',
        'total' => 'numeric|min:0',
        'fully_refunded' => 'boolean',
        'sent' => 'required|boolean',
        'sent_at' => 'date',
        'email_sent' => 'required|boolean',
        'status_id' => 'required|integer',
    );

    /**
     * The custom messages used for the validation
     *
     * @var array
     */
    public static $messages = array(
        //
    );

    /**
     * Paid status id
     *
     * @var string
     */
    const PAID_STATUS_ID = 1;

    /**
     * Unpaid status id
     *
     * @var string
     */
    const UNPAID_STATUS_ID = 2;

    /**
     * Partially paid status id
     *
     * @var string
     */
    const PARTIALLY_PAID_STATUS_ID = 3;

    /**
     * Cancelled status id
     *
     * @var string
     */
    const CANCELLED_STATUS_ID = 4;

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function domain()
    {
        return $this->hasOne(Domain::class, 'id', 'domain_id');
    }

    public function type()
    {
        return $this->hasOne(InvoiceType::class, 'id', 'type_id');
    }

    public function status()
    {
        return $this->hasOne(InvoiceStatus::class, 'id', 'status_id');
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id', 'id')->orderBy('id', 'DESC');
    }

    public static function getMaxId()
    {
        $max= DB::table('INV_invoice')->max('id');

        return $max;
    }

    /**
     * Returns all the invoices for the domains in the array
     *
     * @param $array array
     * @return object
     */
    public static function getInvoicesByDomains($array)
    {
        $results = Invoice::whereIn('domain', $array)->get();

        return $results;
    }

    /**
     * Returns all the sent and unpaid invoices, except booking invoices
     *
     * @return object
     */
    public static function getSentUnpaidInvoices()
    {
        $columns = array(
            'INV_invoice.*',
        );

        $results = Invoice::select($columns)
            ->where('INV_invoice.sent', '=', 1)
            ->where('INV_invoice.type_id', '<>', 4)
            ->whereIn('INV_invoice.status_id', InvoiceStatus::getAllCanCreatePaymentId())
            ->get();

        return $results;
    }

    /**
     * Returns all the unsent invoices, except booking invoices
     *
     * @return object
     */
    public static function getUnsentInvoices()
    {
        $columns = array(
            'INV_invoice.*',
        );

        $results = Invoice::select($columns)
            ->where('INV_invoice.sent', '=', 0)
            ->where('INV_invoice.type_id', '<>', 4)
            ->whereIn('INV_invoice.status_id', InvoiceStatus::getAllCanCreatePaymentId())
            ->get();

        return $results;
    }

    /**
     * 'Fetch all' function
     *
     * @param $query Builder
     * @param $paginationData array
     * @param $search_by string
     * @param $where array
     * @param $whereNot array
     * @param $from_date string
     * @param $to_date string
     * @return object
     */
    public function scopefetchAll(Builder $query, $paginationData = array(), $search_by = null,  $where = array(), $whereNot = array(), $from_date = null, $to_date = null)
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

        /*BEGIN WHERE NOT*/
        if(!empty($whereNot)) {

            foreach ($whereNot as $wn_column => $wn_value) {
                $results = $results->where($wn_column, '<>', $wn_value);
            }
        }
        /*END WHERE NOT*/

        if($from_date != null) {
            $results = $results->where($this::getTable().'.created_at', '>=', date_format(date_create($from_date), 'Y-m-d 00:00:00'));
        }

        if($to_date != null) {
            $results = $results->where($this::getTable().'.created_at', '<=', date_format(date_create($to_date), 'Y-m-d 23:59:59'));
        }

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

    /**
     * Returns the overpaid invoices within a data range
     *
     * @param $from_date string
     * @param $to_date string
     *
     * @return object
     */
    public static function getOverpaidInvoices($from_date, $to_date)
    {
        $columns = array(
            'INV_invoice.*',
        );

        $results = Invoice::select($columns)
            ->where('INV_invoice.total', '>', 0)
            ->where('INV_invoice.created_at', '>=', $from_date)
            ->where('INV_invoice.created_at', '<=', $to_date)
            ->get();

        if(count($results) > 0) {
            foreach ($results as $key => $result) {
                if( $result->outstanding() >= 0 ) {
                    unset($results[$key]);
                }
            }
        }

        return $results;
    }

    /**
     * Returns the outstanding amount
     *
     * @param $Invoice Invoice|null
     * @return float
     */
    public function outstanding($Invoice = null)
    {
        if($Invoice == null) {
            $Invoice = $this;
        }

        $outstanding = 0.00;

        if($Invoice->status_id == self::CANCELLED_STATUS_ID) {
            return $outstanding;
        }

        $total = round(floatval($Invoice->total), 2);

        $total_received = $Invoice->getTotalReceived();
        $total_refunded = $Invoice->getTotalRefunded();

        if($total > 0) {

            if($total_received == 0) {
                $outstanding = $total;
            }else{

                if( ($total_received > 0) AND ($total_refunded == 0) ) {
                    $outstanding = $total - $total_received;
                }

                if( ($total_received > 0) AND ($total_refunded > 0) ) {

                    if($total == $total_received + $total_refunded) {

                        $outstanding = $total - $total_received;

                    }elseif($total > $total_received + $total_refunded){

                        $outstanding = $total - $total_received;

                    }elseif ($total < $total_received + $total_refunded) {

                        if($total == $total_received) {

                            $outstanding = 0;

                        }elseif ($total > $total_received) {

                            $outstanding = $total - $total_received;

                        }elseif ($total < $total_received) {

                            if($total + $total_refunded >= $total_received) {

                                $outstanding = 0;

                            }else{

                                $outstanding = $total - ($total_received - $total_refunded);
                                
                            }

                        }

                    }
                }

            }

        }elseif ($total == 0) {

            if($total_received > 0) {
                $outstanding = $total - ($total_received - $total_refunded);
            }

        }
        
        return round($outstanding, 2);
    }

    /**
     * Returns the maximum amount that can be refunded
     *
     * @param $Invoice null|Invoice
     * @return float
     */
    public function getCanRefundMaxAmount($Invoice = null)
    {
        if($Invoice == null) {
            $Invoice = $this;
        }

        $value = $Invoice->getTotalReceived() - $Invoice->getTotalRefunded();

        return round($value, 2);
    }

    /**
     * Check if can accept payments
     *
     * @param $Invoice null|Invoice
     * @return bool
     */
    public function canAcceptPayments($Invoice = null)
    {
        if($Invoice == null) {
            $Invoice = $this;
        }

        /*Check if can create payments*/
        if($Invoice->status->can_create_payment == 0) {
            return false;
        }

        /*Booking*/
        if($Invoice->type->is_booking_type == 1) {

            if($Invoice->booking->hasConflicts()['response'] == true) {
                return false;
            }
        }

        /*Check outstanding*/
        if($Invoice->outstanding() <= 0)  {
            return false;
        }

        return true;
    }

    /**
     * Check if can accept refunds
     *
     * @param $Invoice null|Invoice
     * @return bool
     */
    public function canAcceptRefunds($Invoice = null)
    {
        if($Invoice == null) {
            $Invoice = $this;
        }

        $total_received = round($Invoice->getTotalReceived(), 2);
        $total_refunded = round($Invoice->getTotalRefunded(), 2);

        $response = ($total_refunded < $total_received) ? true : false;

        return $response;
    }

    /**
     * Returns the total received amount
     *
     * @param $Invoice null|Invoice
     * @return float
     */
    public function getTotalReceived($Invoice = null)
    {
        if($Invoice == null) {
            $Invoice = $this;
        }

        $value = InvoicePayment::where('invoice_id', $Invoice->id)->where('refund', 0)->sum('value');

        return round(floatval($value), 2);
    }

    /**
     * Returns the total refunded amount
     *
     * @param $Invoice null|Invoice
     * @return float
     */
    public function getTotalRefunded($Invoice = null)
    {
        if($Invoice == null) {
            $Invoice = $this;
        }

        $value = InvoicePayment::where('invoice_id', $Invoice->id)->where('refund', 1)->sum('value');

        return round(floatval($value), 2);
    }

    /**
     * Returns the due date
     *
     * @param $Invoice null|Invoice
     * @return string
     */
    public function getDueDate($Invoice = null)
    {
        if($Invoice == null) {
            $Invoice = $this;
        }

        $balance_due_days = $Invoice->type->balance_due_days;

        $created_at = date_format(date_create($Invoice->created_at), 'Y-m-d');

        $due_date = new \DateTime($created_at);
        $due_date->modify("+".$balance_due_days." day");
        $due_date = $due_date->format('Y-m-d');

        return $due_date;
    }


}