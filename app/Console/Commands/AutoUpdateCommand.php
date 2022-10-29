<?php

namespace App\Console\Commands;

use App\Models\Summoner as SummonerModel;
use App\Traits\RiotApiTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AutoUpdateCommand extends Command
{
    protected $signature = 'matches:auto-update';

    protected $description = 'Update matches where summoner->auto_update is true';

    use RiotApiTrait;

    public function handle()
    {
        $results = new Collection([]);
        $summoners = SummonerModel::where('auto_update', true)->get();
        foreach ($summoners as $summoner) {
            $results = $results->merge($this->updateSummonerMatches($summoner));
        }
        Log::info($results->count().' Matches added');
    }
}
