<?php

namespace App\Jobs;

use App\Models\Summoner as SummonerModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoUpdateJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        $results = collect([]);
        $riotApi = new \App\Helpers\RiotApi();
        $summoners = SummonerModel::whereAutoUpdate(true)->get();
        foreach ($summoners as $summoner) {
            $results = $results->merge($riotApi->updateSummonerMatches($summoner));
        }

        Log::info($results->count().' Matches added');
    }

    public function uniqueId()
    {
        return 2;
    }
}
