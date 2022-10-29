<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearMatchesCommand extends Command
{
    protected $signature = 'matches:clear';

    protected $description = 'delete 1 summoner_matches when there are 2 with the same summoner_id and match_id';

    public function handle()
    {
        $ids = collect(DB::select('select t.id
                from (
                    select id,
                        count(*) as cnt
                    from summoner_matches
                    group by summoner_id, match_id
                ) as t
                where cnt > 1'))->map(function ($item) {
            //return $item->id as string;
            return (string) $item->id;
        });

        if ($ids->isNotEmpty()) {
            DB::delete('delete from summoner_matches where id in ('.implode(', ', $ids->toArray()).')');
        }
        Log::info('clear matches: '.$ids->count());
    }
}
