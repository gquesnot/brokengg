<?php

namespace App\Http\Livewire;

use App\Data\FiltersData;
use App\Models\Summoner;
use App\Models\SummonerMatch;
use App\Traits\PaginateTrait;
use App\Traits\QueryParamsTrait;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;

class Champions extends Component
{
    use PaginateTrait;
    use QueryParamsTrait;

    public Summoner $me;

    public $version;

    public FiltersData $filters;

    public function mount(Summoner $me, $version, FiltersData $filters)
    {
        $this->fill([
            'me' => $me,
            'version' => $version,
            'filters' => $filters,
        ]);
    }

    public function render()
    {
        $query = SummonerMatch::whereSummonerId($this->me->id)
            ->filters($this->filters);
        $championIds = SummonerMatch::whereSummonerId($this->me->id)
            ->filters($this->filters)
            ->select(DB::raw('champion_id, count(*) as total'))
            ->groupBy('champion_id')
            ->orderByDesc('total')
            ->pluck('champion_id');

        $champions = SummonerMatch::whereSummonerId($this->me->id)
            ->filters($this->filters)
            ->championsCalc($championIds->forPage($this->page, $this->perPage))
            ->get()
            ->each(function (SummonerMatch $champion) {
                $champion->append([
                    'loses',
                    'avg_kda',
                    'winrate',
                    'avg_damage_dealt_to_champions',
                    'avg_gold_earned',
                    'avg_damage_taken',
                    'avg_gold',
                    'avg_cs',
                ]);
            });

        return view('livewire.champions', ['champions' => new LengthAwarePaginator($champions, $championIds->count(), $this->perPage, $this->page)]);
    }
}
