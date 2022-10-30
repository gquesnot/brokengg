<?php

namespace App\Jobs;

use App\Models\Summoner as SummonerModel;
use App\Traits\RiotApiTrait;
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
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RiotApiTrait;

    public function __construct()
    {
    }

    public function handle()
    {
        $results = collect([]);
        $summoners = SummonerModel::whereAutoUpdate(True)->select(['puuid','last_scanned_match'])->get();
        foreach ($summoners as $summoner) {
            $results = $results->merge($this->updateSummonerMatches($summoner));
        }
        Log::info($results->count().' Matches added');
        app(\Illuminate\Bus\Dispatcher::class)->dispatch(new \App\Jobs\UpdateMatchesJob());
    }
}
