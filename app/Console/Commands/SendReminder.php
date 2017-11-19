<?php

namespace App\Console\Commands;

use App\Jobs\SendEmail;
use App\Models\Settings\Settings;
use Illuminate\Console\Command;

class SendReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:reminder';

    /**
     * Api uri
     *
     * @var string
     */
    protected $api_uri = '/api/domains/domain-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates jobs for the reminder emails';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /*Global settings*/
        $global_settings = Settings::getSettings();

        $data = array(
            'token' => $global_settings->api_token,
            'params' => json_encode(array(
                'key' => 'reminder',
            ))
        );

        $query = http_build_query($data);

        $job_url = $global_settings->app_url.$this->api_uri.'?'.$query;

        dispatch(new SendEmail($job_url));

        return true;
    }
}
