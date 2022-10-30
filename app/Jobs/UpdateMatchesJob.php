<?php

namespace App\Jobs;

use App\Exceptions\LolApiException;
use App\Models\Champion;
use App\Models\ItemSummonerMatch;
use App\Models\Map;
use App\Models\Matche;
use App\Models\Mode;
use App\Models\Queue;
use App\Models\Summoner;
use App\Models\Summoner as SummonerModel;
use App\Models\SummonerMatch;
use App\Traits\RiotApiTrait;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateMatchesJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RiotApiTrait;



    public function __construct()
    {

    }


    /**
     * @throws LolApiException
     */
    public function handle()
    {
        $this->updateMatches();
    }






}