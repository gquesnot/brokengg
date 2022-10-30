<?php

namespace App\Jobs;

use App\Models\Matche;
use App\Models\Summoner;
use App\Traits\RiotApiTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateMatchJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RiotApiTrait;


    public function __construct(public Summoner $summoner)
    {
    }


    public function handle()
    {
        $result = $this->updateSummonerMatches($this->summoner);
        Log::info($result->count().' Matches added');
        app(\Illuminate\Bus\Dispatcher::class)->dispatch(new \App\Jobs\UpdateMatchesJob());
    }
}