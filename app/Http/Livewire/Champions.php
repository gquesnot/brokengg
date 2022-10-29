<?php

namespace App\Http\Livewire;

use App\Models\Summoner;
use App\Traits\PaginateTrait;
use App\Traits\QueryParamsTrait;
use Livewire\Component;

class Champions extends Component
{
    use PaginateTrait;
    use QueryParamsTrait;

    public Summoner $me;

    public $version;

    public $filters = null;

    public function mount($me, $version, $filters)
    {
        $this->filters = $filters;
        $this->me = $me;
        $this->version = $version;
    }

    public function render()
    {
        // todo optimize this query
        $champions = $this->me->champions()->get();
        // map total matches and filter matches > 0

        if ($this->filters != null) {
            $champions = $champions->map(function ($champion) {
                if ($this->filters['queue'] != null) {
                    $champion->matches = $champion->matches->filter(function ($match) {
                        return $match->match->queue_id == $this->filters['queue'];
                    });
                }
                if ($this->filters['dateStart'] != null) {
                    $champion->matches = $champion->matches->filter(function ($match) {
                        return $match->match->match_creation >= $this->filters['dateStart'];
                    });
                }
                if ($this->filters['dateEnd'] != null) {
                    $champion->matches = $champion->matches->filter(function ($match) {
                        return $match->match->match_creation <= $this->filters['dateEnd'];
                    });
                }

                return $champion;
            });
        }

        $champions = $champions->map(function ($champion) {
            $champion->total = $champion->matches->count();

            return $champion;
        })->filter(function ($champion) {
            $show = $champion->total > 0;
            if ($this->filters != null) {
                if ($this->filters['champion'] != null) {
                    if ($champion->id != $this->filters['champion']) {
                        $show = false;
                    }
                }
            }

            return $show;
        })->map(function ($champion) {
            $champion->win = $champion->matches->where('won', true)->count();
            $champion->lose = $champion->total - $champion->win;
            $champion->winrate = round($champion->win / $champion->total * 100, 1);
            $champion->kills = round($champion->matches->sum('kills') / $champion->total, 1);
            $champion->deaths = round($champion->matches->sum('deaths') / $champion->total, 1);
            $champion->assists = round($champion->matches->sum('assists') / $champion->total, 1);
            $champion->kda = $champion->kills + $champion->assists;
            if ($champion->deaths > 0) {
                $champion->kda = round($champion->kda / $champion->deaths, 2);
            }
            $champion->max_death = $champion->matches->max('deaths');
            $champion->max_kills = $champion->matches->max('kills');
            $champion->cs = round($champion->matches->sum('minions_killed') / $champion->total);
            $champion->gold = round($champion->matches->sum('stats.gold_earned') / $champion->total);
            $champion->avg_damage_dealth = round($champion->matches->sum('stats.total_damage_dealt_to_champions') / $champion->total);
            $champion->avg_damage_taken = round($champion->matches->sum('stats.total_damage_taken') / $champion->total);
            $champion->double_kills = $champion->matches->sum('double_kills');
            $champion->triple_kills = $champion->matches->sum('triple_kills');
            $champion->quadra_kills = $champion->matches->sum('quadra_kills');
            $champion->penta_kills = $champion->matches->sum('penta_kills');

            return $champion;
        })->sortBy(function ($champion) {
            return $champion->total;
        }, SORT_REGULAR, true);

        return view('livewire.champions', ['champions' => $this->paginate($champions)]);
    }
}
