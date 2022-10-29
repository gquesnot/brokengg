<?php

namespace App\Http\Livewire;

use App\Helpers\Stats;
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
        $stats = new Stats(
            SummonerMatch::whereIn('match_id', $matchIds)
                ->whereSummonerId($this->me->id)
                ->select([
                    'kills',
                    'deaths',
                    'assists',
                    'kda',
                    'minions_killed',
                    'kill_participation',
                    'won',
                    'match_id',
                    'champion_id',
                    'id',
                ])->toBase()->get()
        );

        $paginator = new LengthAwarePaginator($matches, $matchIds->count(), $this->perPage, $this->page);

        return view('livewire.matches', ['matches' => $paginator, 'stats' => $stats]);
    }
}
