<?php

namespace App\Http\Livewire;

use App\Models\Champion;
use App\Models\Matche;
use App\Models\Queue;
use App\Models\Summoner;
use App\Models\SummonerMatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Session;
use Surgiie\Transformer\DataTransformer;

class Filter extends Component
{
    public array $filters = [
        'queue' => null,
        'dateStart' => null,
        'dateEnd' => null,
        'champion' => null,
        'filterEncounters' => null,
    ];

    public array $options;

    public Collection $matchesIds;

    public Summoner $me;

    public function mount(Summoner $me)
    {
        $this->me = $me;
        $this->matchesIds = collect(DB::select('SELECT match_id FROM summoner_matches WHERE  summoner_id = ?', [$me->id]))->pluck('match_id');

        $matches = Matche::whereIn('id', $this->matchesIds)->orderByDesc('match_creation')->pluck('id');
        $recent_champions = SummonerMatch::whereIn('match_id', $matches->forPage(1, 50))->where('summoner_id', $me->id)->select(['champion_id', DB::raw('count(*) as total')])->groupBy('champion_id')->orderBy('total', 'DESC')->limit(5)->with('champion')->get()->pluck('champion');
        $most_played_champions = SummonerMatch::whereIn('match_id', $this->matchesIds)->where('summoner_id', $me->id)->select(['champion_id', DB::raw('count(*) as total')])->groupBy('champion_id')->orderBy('total', 'DESC')->limit(5)->with('champion')->get()->pluck('champion');
        # combine both
        $champions = $recent_champions->merge($most_played_champions)->unique();
        $champion_options =[
            'Most and recently played' =>$champions->map(function ($champion) {
                return [
                    'value' => $champion->id,
                    'label' => $champion->name,
                ];
            })->toArray(),
            "All" => Champion::all(['name', 'id'])->map(function ($champion) {
                return [
                    'value' => $champion->id,
                    'label' => $champion->name,
                ];
            })->toArray()
        ];
        $this->options = [
            'queue' => Queue::whereIn('id', collect(DB::select('select distinct queue_id from matches where id in (SELECT match_id FROM summoner_matches WHERE  summoner_id = ?)', [$me->id]))->pluck('queue_id'))->get(['id', 'description'])->map(function ($queue) {
                return [
                    'value' => $queue->id,
                    'label' => str_replace('Pick','', str_replace(' games', '', $queue->description)),
                ];
            })->toArray(),
            'champion' => $champion_options,
        ];


        //dd($this->options);
    }

    public function clearFilter($update = true)
    {
        $this->reset('filters');
        $this->dispatchBrowserEvent('select2-clear');
        $this->resetErrorBag();
        if ($update) {
            $this->emit('filtersUpdated', $this->filters);

        }

        Session::flash('success', 'Filters cleared');
    }


    public function applyFilters()
    {


        $filters = $this->validate([
            'filters.dateStart' => 'nullable|date',
            'filters.dateEnd' => 'nullable|date|after:filters.dateStart',
            'filters.champion' => 'nullable|integer|exists:champions,id',
            'filters.queue' => 'integer|nullable|exists:queues,id',
            'filters.filterEncounters' => 'nullable|boolean',
        ],[
            'filters.dateEnd.after' => 'The end date must be after the start date',
            'filters.champion.exists' => 'The selected champion is invalid',
            'filters.queue.exists' => 'The selected queue is invalid',
        ])['filters'];

        foreach ($filters as $key => $filter) {
            if (!$filter) {
                $filter = null;
            } else {
                if (in_array($key, ['champion', 'queue'])) {
                    $filter = intval($filter);
                }
            }
            $this->filters[$key] = $filter;
        }

        $this->emit('filtersUpdated', $this->filters);
        Session::flash('success', 'Filters applied');
    }

    public function render()
    {
        return view('livewire.filter');
    }
}
