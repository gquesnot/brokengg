<?php

namespace App\Console\Commands;

use App\Jobs\CompleteSummonerJob;
use Illuminate\Console\Command;

class lolSummonerUpdateCommand extends Command
{
    protected $signature = 'lol:update-summoner';

    protected $description = 'Command description';

    public function handle()
    {
        $this->info('Starting summoner update');
        $bar = $this->output->createProgressBar(1400);
        $bar->setFormat('very_verbose');

        $bar->start();
        for ($i = 0; $i < 35; $i++) {
            CompleteSummonerJob::dispatchSync();
            $bar->advance(40);
        }
        $bar->finish();
    }
}
