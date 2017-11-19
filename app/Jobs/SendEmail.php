<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Bus\SelfHandling;

class SendEmail extends Job implements ShouldQueue, SelfHandling
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Url
     *
     * @var string
     */
    protected $url;

    /**
     * Create a new job instance.
     *
     * @param $url string
     * @return void
     */
    public function __construct($url)
    {
        /*Url*/
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = file_get_contents($this->url);
        echo $result;
    }
}
