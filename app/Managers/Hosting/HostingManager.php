<?php

namespace App\Managers\Hosting;

use App\Jobs\SendEmail;
use App\Managers\Invoice\InvoiceManager;
use App\Models\Domain\DomainRenewalEntry;
use App\Models\Domain\Interval;
use App\Models\Settings\Settings;
use Auth;
use Config;
use Illuminate\Support\Facades\App;

class HostingManager
{

    /**
     * Return the domain expiry date
     *
     * @param $date date
     * @param $period int
     * @param $interval_id int
     *
     * @return date
     */
    public function setTheExpiryDate($date, $period, $interval_id){

        /*Set the expiry date based on the the registration period*/
        $Interval = Interval::where('id', $interval_id)->first();

        $expiry_date = new \DateTime($date);
        $expiry_date->modify('+'.$period.' '.$Interval->code);
        $expiry_date = $expiry_date->format('Y-m-d');

        return $expiry_date;

    }

    /**
     * Get the logged in user
     *
     *
     * @return object
     */
    public function getTheLoggedInUser(){

        $user_id = null;

        $User = Auth::User();

        if($User != null) {
            $user = $User;
        }

        return $user;

    }

    /**
     * Send a domain email
     *
     * @param $user_id int
     * @param $domain_id int
     * @param $key string
     *
     * @return bool
     */
    public function sendDomainEmail($user_id, $domain_id, $key)
    {

        /*Global settings*/
        $global_settings = Settings::getSettings();

        $api_config = Config::get('api');

        $endpoint = isset($api_config['endpoints']['domains']['send-email']) ? $api_config['endpoints']['domains']['send-email']: null;

        if($endpoint != null) {
            $array['url'] = $global_settings->app_url.$api_config['api'].$endpoint;
        }

        $data = array(
            'token' => $global_settings->api_token,
            'params' => json_encode(array(
                'user_id' => $user_id,
                'domain_id' => $domain_id,
                'key' => $key
            ))
        );
        
        $query = http_build_query($data);

        /*Dispatch a job when a domain is assigned to an user*/
        $job_url = $array['url'].'?'.$query;

        /*Dispatch job*/
        dispatch(new SendEmail($job_url));

    }

    /**
     * Creates a domain invoice and updates the domain with the new invoice
     *
     * @param $Domain Domain
     * @return true
     */
    public function generateInvoice($Domain)
    {
        $invoiceManager = App::make(InvoiceManager::class);

        $invoice_id = $invoiceManager->generateDomainInvoice($Domain, null);

        /*Update the booking with the new invoice id*/
        $Domain->invoice_id = $invoice_id;
        $Domain->update();

        return true;
    }

    /**
     * Creates a domain renewal invoice and updates the domain renewal with the new invoice
     *
     * @param $DomainRenewalEntry DomainRenewalEntry
     * @return true
     */
    public function generateDomainRenewalEntryInvoice($DomainRenewal)
    {
        $invoiceManager = App::make(InvoiceManager::class);

        $invoice_id = $invoiceManager->generateDomainRenewalInvoice($DomainRenewal);

        /*Update the booking with the new invoice id*/
        $DomainRenewal->invoice_id = $invoice_id;
        $DomainRenewal->update();

        return true;
    }

}