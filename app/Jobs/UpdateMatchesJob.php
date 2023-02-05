<?php

namespace App\Jobs;

use App\Models\Summoner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateMatchesJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function timeout()
    {
        return 60 * 60;
    }

    public function uniqueId()
    {
        return 1;
    }

    public function backoff()
    {
        return 30;
    }

    public function __construct(public ?int $summonerId)
    {
    }

    public function handle()
    {
        if (! $this->summonerId) {
            Summoner::whereAutoUpdate(true)->cursor()->each(function ($summoner) {
                $summoner->updateMatches();
            });
        } else {
            Summoner::find($this->summonerId)->updateMatches();
        }
    }
}
