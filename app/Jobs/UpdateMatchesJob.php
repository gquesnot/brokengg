<?php

namespace App\Jobs;

use App\Exceptions\LolApiException;
use App\Helpers\RiotApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateMatchesJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function timeout()
    {
        return 60 * 60;
    }

    public function uniqueId()
    {
        return 1;
    }

    public function backoff(){
        return 30;
    }

    public function __construct()
    {

    }


    /**
     * @throws LolApiException
     */
    public function handle()
    {
        $riotApi = new RiotApi();
        $riotApi->updateMatches();
        $riotApi->reset();
        $riotApi->clearMatches();
        #$riotApi->updateIncompleteSummoners();
    }


}