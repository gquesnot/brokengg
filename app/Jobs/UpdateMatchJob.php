<?php

namespace App\Jobs;

use App\Helpers\RiotApi;
use App\Models\Summoner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateMatchJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private RiotApi $riotApi;

    public function __construct(public Summoner $summoner)
    {
    }

    public function uniqueId()
    {
        return 3;
    }

    public function handle()
    {
        $this->riotApi = new RiotApi();
        $result = $this->riotApi->updateSummonerMatches($this->summoner);
        Log::info($result->count().' Matches added');
    }
}
