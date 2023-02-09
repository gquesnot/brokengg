<?php

namespace App\Jobs;

use App\Exceptions\RiotApiForbiddenException;
use App\Models\Summoner;
use App\Models\SummonerMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompleteSummonerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        try {
            $summoner_not_founds = Summoner::whereComplete(false)->pluck('id');
            $most_found_summoner = SummonerMatch::groupBy('summoner_id')
                ->whereIn('summoner_id', $summoner_not_founds)
                ->select(DB::raw('summoner_id, count(*) as count'))
                ->orderByDesc('count')
                ->limit(40)
                ->pluck('summoner_id')
                ->toArray();
            Summoner::whereIn('id', $most_found_summoner)->cursor()->each(function ($summoner) {
                $summoner->selfUpdate(true);
            });
        } catch (RiotApiForbiddenException $e) {
            Log::error('RiotApiForbiddenException: '.$e->getMessage());
        }
    }
}
