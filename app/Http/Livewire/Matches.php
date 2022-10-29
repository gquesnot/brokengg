<?php

namespace App\Http\Livewire;

use App\Models\Summoner;
use App\Models\SummonerMatch;
use App\Traits\PaginateTrait;
use App\Traits\QueryParamsTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;

class Matches extends Component
{
    use PaginateTrait;
    use QueryParamsTrait;

    public Summoner $me;

    public $version;

    public $stats = null;

    public $filters = null;

    public function mount($me, $version, $filters)
    {
        $this->me = $me;
        $this->version = $version;
        $this->filters = $filters;
    }

    public function showVersus($id)
    {
        $this->emit('showVersus', $id);
    }

    public function render()
    {
        $matchIds = $this->me->getCachedMatchesQuery($this->filters);
        $encountersMatchIds = $this->filters['filterEncounters'] ? $matchIds : $this->me->getCachedMatchesQuery();
        $encounters = $this->me->getCachedEncounters($encountersMatchIds, $this->filters['filterEncounters'] ? $this->filters : []);

        $matches = SummonerMatch::whereIn('match_id', $matchIds->forPage($this->page, $this->perPage))
            ->where('summoner_id', $this->me->id)
            ->select([
                'kills',
                'deaths',
                'assists',
                'kda',
                'champ_level',
                'minions_killed',
                'kill_participation',
                'won',
                'match_id',
                'champion_id',
                'id',
                'summoner_id',
            ])
            ->with('items:name,img_url')
            ->with('champion:id,name,img_url')
            ->with('match:id,since_match_end,match_duration,mode_id')
            ->with('match.mode:id,name')
            ->with('match.participants:id,summoner_id,won,champion_id')
            ->with('match.participants.champion:id,name,img_url')
            ->with('match.participants.summoner:id,name')
            ->get();

        $matches = $matches->sortBy(function (SummonerMatch $match) {
            return $match->match->match_creation;
        }, SORT_REGULAR, true)->map(function (SummonerMatch $match) use ($encounters) {
            $match->match->setAttribute('participants', $match->match->participants->map(function ($participant) use ($encounters) {
                if ($encounters->has($participant->summoner_id)) {
                    $participant->setAttribute('total', $encounters->get($participant->summoner_id));
                } else {
                    $participant->setAttribute('total', 1);
                }

                return $participant;
            }));

            return $match;
        });

        $this->stats = $this->getStat($matchIds);
        $paginator = new LengthAwarePaginator($matches, $matchIds->count(), $this->perPage, $this->page);

        return view('livewire.matches', ['matches' => $paginator]);
    }

    public function getStat($matchIds)
    {
        $matches = SummonerMatch::whereIn('match_id', $matchIds)
            ->where('summoner_id', $this->me->id)
            ->select(
                'kill_participation',
                'kda',
                'won'
            )
            ->get();
        $tmp = [
            'kill_participation' => 0,
            'kda' => 0,
            'game_won' => 0,
            'win_percent' => 0,
            'game_lose' => 0,
            'game_played' => 0,
            'lose' => 0,
        ];
        if (! $matches->isEmpty()) {
            foreach ($matches as $match) {
                $tmp['kill_participation'] += $match->kill_participation;
                $tmp['kda'] += $match->kda;
                $tmp['game_won'] += $match->won;
                $tmp['game_lose'] += ! $match->won;
                $tmp['game_played'] += 1;
            }
            $tmp['kill_participation'] = round($tmp['kill_participation'] / $tmp['game_played'] * 100);
            $tmp['kda'] = round($tmp['kda'] / $tmp['game_played'], 2);
            $tmp['win_percent'] = round($tmp['game_won'] / $tmp['game_played'] * 100);
        }

        return $tmp;
    }
}
