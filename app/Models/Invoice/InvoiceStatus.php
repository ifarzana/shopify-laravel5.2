<?php

namespace App\Models\Invoice;

use Illuminate\Database\Eloquent\Model;
use Config;

class InvoiceStatus extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'INV_invoice_status';

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
    protected $fillable = ['name', 'tr_class', 'status_class', 'can_create_payment', 'can_refund', 'can_send_email', 'can_cancel_ground_rent'];

    /**
     * Returns an array with all the statuses where the can_create_payment = 1
     *
     * @return int
     */
    public static function getAllCanCreatePaymentId()
    {
        $results = InvoiceStatus::where('can_create_payment', 1)->pluck('id')->toArray();
        return $results;
    }

}