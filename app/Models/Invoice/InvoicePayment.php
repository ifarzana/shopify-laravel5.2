<?php

namespace App\Models\Invoice;

use App\Models\PaymentMethod\PaymentMethod;
use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\SearchableTrait;
use Config;
use Illuminate\Support\Facades\DB;

class InvoicePayment extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'INV_invoice_payment';
    
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
    protected $fillable = ['invoice_id', 'value', 'payment_method_id', 'refund', 'refunded_online','added_by_user_id', 'added_by_user_name', 'transaction_details', 'created_at'];

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
        'invoice_id' => 'required|integer',
        'value' => 'required|numeric|min:0.01',
        'payment_method_id' => 'required|integer',
        'refund' => 'required|boolean',
        'refunded_online' => 'boolean',
        'added_by_user_id' => 'integer',
        'added_by_user_name' => 'required',
        'created_at' => 'date'
    );

    /**
     * The custom messages used for the validation
     *
     * @var array
     */
    public static $messages = array(
        'value.max' => 'The amount may not be greater than the outstanding balance.',
        'created_at.date' => 'The date/time field is invalid.',
        'created_at.alpha' => 'The date/time field is invalid.',
    );

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'id', 'invoice_id');
    }

    public function paymentMethod()
    {
        return $this->hasOne(PaymentMethod::class, 'id', 'payment_method_id');
    }

    /**
     * Returns all the payments for the invoices ids in the array created before date
     *
     * @param $array array
     * @param $created_before_date null|string
     *
     * @return object
     */
    public static function getPaymentsByInvoicesAndDate($array, $created_before_date = null)
    {
        $Payments = InvoicePayment::whereIn('invoice_id', $array);

        if($created_before_date != null) {
            $Payments->where('created_at', '<=', $created_before_date);
        }

        $Payments = $Payments->get();

        return $Payments;
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
     * Returns the private owners payments within 2 dates
     *
     * @param $from_date string
     * @param $to_date string
     * @param $payment_method_id int
     *
     * @return object
     */
    public static function getCaravansTransactions($from_date, $to_date, $payment_method_id)
    {
        $columns = array(
            'INV_invoice_payment.*',
            'INV_invoice.reference as invoice_reference',
            'INV_invoice.customer_id as invoice_customer_id',
            'INV_invoice.caravan_id as invoice_caravan_id',
            DB::raw("CONCAT(CUST_customer.first_name,' ',CUST_customer.last_name) as customer_fullname"),
            'INV_invoice_type.name as invoice_type',
            'CONF_payment_method.name as payment_method',
            'FL_caravan.pitch_id as caravan_pitch_id',
            'PIT_pitch.number as caravan_pitch_number'
        );

        $results = InvoicePayment::select($columns)
            ->whereBetween('INV_invoice_payment.created_at', array($from_date, $to_date))
            ->join('INV_invoice','INV_invoice_payment.invoice_id', '=', 'INV_invoice.id' , 'left')
            ->join('INV_invoice_type','INV_invoice.type_id', '=', 'INV_invoice_type.id' , 'left')
            ->join('CUST_customer','INV_invoice.customer_id', '=', 'CUST_customer.id' , 'left')
            ->join('CONF_payment_method','INV_invoice_payment.payment_method_id', '=', 'CONF_payment_method.id' , 'left')

            ->join('FL_caravan','INV_invoice.caravan_id', '=', 'FL_caravan.id' , 'left')
            ->join('PIT_pitch','FL_caravan.pitch_id', '=', 'PIT_pitch.id' , 'left')

            ->whereNotNull('INV_invoice.caravan_id')
            ->orderBy('INV_invoice_payment.created_at', 'ASC')
            ->groupBy('INV_invoice_payment.id');

        /*Payment method id*/
        if($payment_method_id != null) {
            $results->where('CONF_payment_method.id', '=', (int)$payment_method_id);
        }

        $results = $results->get();

        return $results;
    }

}