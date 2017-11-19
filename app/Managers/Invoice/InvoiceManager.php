<?php

namespace App\Managers\Invoice;

use App\Models\Currency\Currency;
use App\Models\Domain\Domain;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceSettings;
use App\Models\Invoice\InvoiceTemplate;

use App\Models\Settings\Settings;
use Config;
use Illuminate\Support\Facades\Auth;

class InvoiceManager
{
    /**
     * Invoice settings
     *
     * @var object
     */
    protected $invoice_settings;

    /**
     * Domain settings
     *
     * @var object
     */
    protected $domain_settings = null;

    /**
     * Currency
     *
     * @var object
     */
    protected $currency;

    /**
     * Fees
     *
     * @var object
     */
    protected $fees;

    /**
     * Image config
     *
     * @var array
     */
    protected $image_config;

    /**
     * Construct - set all arrays and objects
     *
     * @return void
     */
    public function __construct()
    {
        /*Currency*/
        $this->currency = Currency::getActiveCurrency();
    }

    /**
     * The main function to be called from the controller
     * Generates the booking invoice
     *
     * @param $Domain Domain
     * @param $Invoice Invoice
     *
     * @return int|object
     */
    public function generateDomainInvoice($Domain, $Invoice = null)
    {
        /*Calculate domain registration cost*/
        $cost = $Domain->cost;

        if($Invoice == null) {

            /*Build invoice array*/
            $array = array(
                'currency_id' => $this->currency->id,
                'user_id' => Auth::User()->id,
                'domain_id' => $Domain->id,
                'reference' => $this->generateInvoiceReference(),
                'client_id' => $Domain->client_id,
                'type_id' => 1,
                'total' => $cost,
                'fully_refunded' => 0,
                'sent' => 0,
                'sent_at' => null,
                'email_sent' => 0,
                'status_id' => Invoice::UNPAID_STATUS_ID,
                'filename' => null,
                'created_at' => $Domain->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $Domain->updated_at->format('Y-m-d H:i:s'),
            );

            /*Create the invoice*/
            $Invoice = $this->generateInvoice($array);

            /*Set filename*/
            $filename = $Invoice->reference.'.pdf';

            /*Update invoice's filename*/
            $Invoice->filename = $filename;
            $Invoice->update();

            return $Invoice->id;

        }else{

            /*Get the lines*/
            $lines = $this->calculateDomainLines($Domain);

            /*Calculate total*/
            $total = floatval($Domain->total);

            /*Set the array data for the template*/
            $data['invoice'] = $Invoice;
            $data['Domain'] = $Domain;
            $data['lines'] = $lines;
            $data['total'] = $total;

            /*Get the template*/
            //$template_name =  InvoiceTemplate::getTemplate($Invoice->type_id);
            $template_name =  'invoice.templates.domain';

            $pdf = \PDF::loadView($template_name, $data);

            return $pdf->download($Invoice->filename);
        }

    }

    /**
     * The main function to be called from the controller
     * Generates the booking invoice
     *
     * @param $Domain Domain
     * @param $Invoice Invoice
     *
     * @return int|object
     */
    public function generateDomainRenewalInvoice($DomainRenewal, $Invoice = null)
    {
        /*Get the domain*/
        $Domain = Domain::find($DomainRenewal->domain_id);

        /*Calculate domain registration cost*/
        $cost = $DomainRenewal->cost;

        if($Invoice == null) {

            /*Build invoice array*/
            $array = array(
                'currency_id' => $this->currency->id,
                'user_id' => Auth::User()->id,
                'domain_id' => $Domain->id,
                'reference' => $this->generateInvoiceReference(),
                'client_id' => $Domain->client_id,
                'type_id' => 2,
                'total' => $cost,
                'fully_refunded' => 0,
                'sent' => 0,
                'sent_at' => null,
                'email_sent' => 0,
                'status_id' => Invoice::UNPAID_STATUS_ID,
                'filename' => null,
                'created_at' => $DomainRenewal->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $DomainRenewal->updated_at->format('Y-m-d H:i:s'),
            );

            /*Create the invoice*/
            $Invoice = $this->generateInvoice($array);

            /*Set filename*/
            $filename = $Invoice->reference.'.pdf';

            /*Update invoice's filename*/
            $Invoice->filename = $filename;
            $Invoice->update();

            return $Invoice->id;

        }else{

            /*Get the lines*/
            $lines = $this->calculateDomainLines($Domain);

            /*Calculate total*/
            $total = floatval($Domain->total);

            /*Set the array data for the template*/
            $data['invoice'] = $Invoice;
            $data['Domain'] = $Domain;
            $data['lines'] = $lines;
            $data['total'] = $total;

            /*Get the template*/
            $template_name =  'invoice.templates.domain';

            $pdf = \PDF::loadView($template_name, $data);

            return $pdf->download($Invoice->filename);
        }

    }

    /**
     * Generate invoice reference
     *
     * @return string
     */
    protected function generateInvoiceReference()
    {
        /*Invoice prefix*/
        $invoice_prefix = Settings::getSettings()->invoice_prefix;

        $max = Invoice::getMaxId();

        $max = $max + 1;

        $reference = $invoice_prefix.$max;

        return $reference;
    }

    /**
     * Creates the invoice
     *
     * @param $array array
     * @return object
     */
    public function generateInvoice($array = array())
    {
        $Invoice = Invoice::create($array);
        return $Invoice;
    }

    /**
     * Returns the invoice's object
     *
     * @param $id integer
     * @return object
     */
    public function getObjectByInvoice($id)
    {
        $Invoice =  Invoice::findOrFail($id);

        /*Get the invoice type*/
        $type = $Invoice->type;

        /*Create new object*/
        $object = new $type->object();

        /*Get the object*/
        $object = $object->findByInvoiceId($Invoice->id);

        return $object;
    }

    /**
     * Returns the domain lines
     *
     * @param $Domain Domain
     *
     * @return array
     */
    protected function calculateDomainLines($Domain)
    {
        $array = array(
            'name' => array(),
            'cost' => array()
        );

        return $array;
    }

}