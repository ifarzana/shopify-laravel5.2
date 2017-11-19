<?php

namespace App\Managers\Alert\src;

use App\Models\Domain\Domain;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceType;

abstract class AlertFunctions
{
    /**
     * Date
     *
     * @var string
     */
    protected $date;

    /**
     * Datetime
     *
     * @var string
     */
    protected $datetime;

    /**
     * Construct - set all arrays and objects
     *
     * @return void
     */
    public function __construct()
    {
        /*Date*/
        $this->date = date('Y-m-d');

        /*Datetime*/
        $this->datetime = date('Y-m-d H:i:s');
    }

    /**
     * Domain assigned
     *
     * @return object
     */
    protected function domainsAssignedList()

    {
        $date = $this->date;

        $results = Domain::getDateDomains($date);

        return $results;
    }

    /**
     * Domain Expiring in 30 days
     *
     * @return object
     */
    protected function domainsExpiringInThirtyDaysList()

    {
        $date = $this->date;

        $date = new \DateTime($date);
        $date->modify('+31 day');
        $date = $date->format('Y-m-d');

        $results = Domain::getDateDomainsExpiringInThirtyDays($date);

        return $results;
    }

}