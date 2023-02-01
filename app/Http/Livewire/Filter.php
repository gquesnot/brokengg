<?php

namespace App\Http\Livewire;

use App\Data\FiltersData;
use App\Models\Champion;
use App\Models\Matche;
use App\Models\Queue;
use App\Models\Summoner;
use App\Models\SummonerMatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Session;

class Filter extends Component
{
    public FiltersData $filters;

    public array $options;


    public Summoner $summoner;

    public function mount(Summoner $summoner, FiltersData $filters)
    {
        $this->fill([
            'me' => $summoner,
            'filters' => $filters,
        ]);
        $matchesIds = collect(DB::select('SELECT match_id FROM `summoner_matches` WHERE  summoner_id = ?', [$summoner->id]))->pluck('match_id');

        $matches = Matche::whereIn('id', $matchesIds)->orderByDesc('match_creation')->limit(50)->pluck('id');
        $recent_champions = SummonerMatch::whereIn('match_id', $matches)
            ->where('summoner_id', $summoner->id)
            ->select(['champion_id', DB::raw('count(*) as total')])
            ->groupBy('champion_id')->orderBy('total', 'DESC')
            ->limit(5)
            ->with('champion')
            ->get()
            ->pluck('champion');
        $most_played_champions = SummonerMatch::whereIn('match_id', $matchesIds)
            ->where('summoner_id', $summoner->id)->select(['champion_id', DB::raw('count(*) as total')])
            ->groupBy('champion_id')
            ->orderBy('total', 'DESC')
            ->limit(5)->with('champion')
            ->get()
            ->pluck('champion');
        // combine both
        $champions = $recent_champions->merge($most_played_champions)->unique();
        $champion_options = [
            'Most and recently played' => $champions->map(function ($champion) {
                return [
                    'value' => $champion->id,
                    'label' => $champion->name,
                ];
            })->toArray(),
            'All' => Champion::all(['name', 'id'])->map(function ($champion) {
                return [
                    'value' => $champion->id,
                    'label' => $champion->name,
                ];
            })->toArray(),
        ];
        $this->options = [
            'queue' => Queue::whereIn('id', collect(DB::select('select distinct queue_id from matches where id in (SELECT match_id FROM summoner_matches WHERE  summoner_id = ?)', [$summoner->id]))->pluck('queue_id'))->get(['id', 'description'])->map(function ($queue) {
                return [
                    'value' => $queue->id,
                    'label' => str_replace('Pick', '', str_replace(' games', '', $queue->description)),
                ];
            })->toArray(),
            'champion' => $champion_options,
        ];

        //dd($this->options);
    }

    public function clearFilter($update = true)
    {
        $this->filters = new FiltersData();
        $this->dispatchBrowserEvent('select2-clear');
        $this->resetErrorBag();
        if ($update) {
            $this->emitTo(BaseSummoner::class, 'updateFilters', $this->filters->toArray());

        }

        Session::flash('success', 'Filters cleared');
    }

    public function applyFilters()
    {
        $this->filters->clear_empty();
        $filters = $this->validate([
            'filters.date_start' => 'nullable|date',
            'filters.date_end' => 'nullable|date|after:filters.date_start',
            'filters.champion' => 'nullable|integer|exists:champions,id',
            'filters.queue' => 'integer|nullable|exists:queues,id',
            'filters.filter_encounters' => 'nullable|boolean',
        ], [
            'filters.date_end.after' => 'The end date must be after the start date',
            'filters.champion.exists' => 'The selected champion is invalid',
            'filters.queue.exists' => 'The selected queue is invalid',
        ])['filters'];
        $this->emitTo(BaseSummoner::class, 'updateFilters', $filters);
        Session::flash('success', 'Filters applied');
    }

    public function render()
    {
        return view('livewire.filter');
    }
}
