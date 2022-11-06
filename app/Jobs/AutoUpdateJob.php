<?php

namespace App\Jobs;

use App\Models\Summoner as SummonerModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AutoUpdateJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        $results = collect([]);
        $riotApi = new \App\Helpers\RiotApi();
        # TODO : fix this
        $summoners = SummonerModel::whereAutoUpdate(True)->select(['puuid','last_scanned_match'])->get();
//        foreach ($summoners as $summoner) {
//            $results = $results->merge($riotApi->updateSummonerMatches());
//        }
        Log::info($results->count().' Matches added');
    }

    public function uniqueId(){
        return 2;
    }

}
