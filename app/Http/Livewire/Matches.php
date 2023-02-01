<?php

namespace App\Http\Livewire;

use App\Data\FiltersData;
use App\Helpers\Stats;
use App\Models\Matche;
use App\Models\Summoner;
use App\Models\SummonerMatch;
use App\Traits\PaginateTrait;
use App\Traits\QueryParamsTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Matches extends Component
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
//        $matchIds=  \App\Models\Matche::whereQueueId(900)->pluck('id');
//        $total = 0;
//        $count = 0;
//        foreach (\App\Models\SummonerMatch::whereIn('match_id', $matchIds)->with('match:id,match_duration')->select('id', 'match_id', 'deaths')->cursor() as $summonerMatch) {
//            $count++;
//            $total += $summonerMatch->deaths / $summonerMatch->match->match_duration->minute;
//        }
//        $ratio_arurf = $total / $count;
//
//        $total = 0;
//        $count = 0;
//        $matchIds=  \App\Models\Matche::whereQueueId(1900)->pluck('id');
//        foreach (\App\Models\SummonerMatch::whereIn('match_id', $matchIds)->with('match:id,match_duration')->select('id', 'match_id', 'deaths')->cursor() as $summonerMatch) {
//            $count++;
//            $total += $summonerMatch->deaths / $summonerMatch->match->match_duration->minute;
//        }
//        $ratio_urf = $total /$count;
//
//        dd('ARURF: ' . $ratio_arurf . ' URF: ' . $ratio_urf);
//


    }

    public function showVersus($id)
    {
        $this->emit('showVersus', $id);
    }

    public function getMatches($count): Collection
    {
        return Cache::remember($this->me->getCacheKey('matches_view', $this->filters, $count), 60 * 5, function () {
            $matches_data = $this->me->getMatchesCache($this->filters);
            $matches = SummonerMatch::whereIn('summoner_matches.match_id', $matches_data['match_ids']->forPage($this->page, $this->perPage))
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
                    'summoner_matches.match_id',
                    'champion_id',
                    'summoner_matches.id',
                    'summoner_id',
                ])
                ->with('items:name,img_url')
                ->with('champion:id,name,img_url')
                ->with('match:id,match_duration,mode_id,match_creation,match_end')
                ->with('match.mode:id,name')
                ->with('match.participants:id,summoner_id,won,champion_id')
                ->with('match.participants.champion:id,name,img_url')
                ->with('match.participants.summoner:id,name')
                ->join('matches', 'matches.id', '=', 'summoner_matches.match_id')
                ->orderBy('matches.match_creation', 'desc')
                ->get();

            return $matches->map(function (SummonerMatch $match) use ($matches_data) {
                $match->match->setAttribute('participants', $match->match->participants->map(function ($participant) use ($matches_data) {
                    if ($matches_data['encounters']->has($participant->summoner_id)) {
                        $participant->setAttribute('total', $matches_data['encounters']->get($participant->summoner_id));
                    } else {
                        $participant->setAttribute('total', 1);
                    }

                    return $participant;
                }));

                return $match;
            });
        });
    }

    public function getStats($count): Stats
    {
        return Cache::remember($this->me->getCacheKey('stats_view', $this->filters, $count), 60 * 5, function () {
            $matches_data = $this->me->getMatchesCache($this->filters);
            return new Stats(
                SummonerMatch::whereIn('match_id', $matches_data['match_ids'])
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
        });

    }


    public function render()
    {
        $count = $this->me->getMatchesCount($this->filters);
        $matches = $this->getMatches($count);
        return view('livewire.matches', [
            'matches' => new LengthAwarePaginator($matches, $matches->count(), $this->perPage, $this->page),
            'stats' => $this->getStats($count),
        ]);
    }
}
