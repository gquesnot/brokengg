<?php

namespace App\Jobs;

use App\Models\ApiAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class UpdateRiotKeysJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {

        $accounts = ApiAccount::all();
        $js_path = resource_path('js\\lolTokens\\index.js');
        $accounts->each(function ($account) use ($js_path) {
            $process = new Process(['node', $js_path, $account->username, $account->password]);
            $process->run();
            if ($process->isSuccessful()) {
                $account->api_key = Str::of($process->getOutput())->trim();
            }
            $account->actif = $process->isSuccessful();
            $account->save();
        });
    }

    public function timeout()
    {
        return 360;
    }
}