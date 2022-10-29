<?php

namespace App\Http\Livewire;

use App\Models\Champion;
use App\Models\Queue;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Session;

class Filter extends Component
{
    public array $filters;

    public array $options;

    public string $matchesIds;

    public $me;

    public function mount($me)
    {
        $this->me = $me;
        $this->matchesIds = collect(DB::select('SELECT match_id FROM summoner_matches WHERE  summoner_id = ?', [$me->id]))->pluck('match_id');

        //$this->clearFilter(false);
        $this->options = [
            'queue' => Queue::whereIn('id', collect(DB::select('select distinct queue_id from matches where id in (SELECT match_id FROM summoner_matches WHERE  summoner_id = ?)', [$me->id]))->pluck('queue_id'))->get(['id', 'description'])->map(function ($queue) {
                return [
                    'id' => $queue->id,
                    'description' => str_replace(' games', '', $queue->description),
                ];
            }),
            'status' => ['win', 'lose'],
            'champion' => Champion::all(['name', 'id']),
        ];

        //dd($this->options);
    }

    public function clearFilter($update = true)
    {
        $this->filters = [
            'status' => null,
            'queue' => null,
            'dateStart' => null,
            'dateEnd' => null,
            'champion' => null,
            'filterEncounters' => null,

        ];
        if ($update) {
            $this->emit('filtersUpdated', $this->filters);
        }
        Session::flash('success', 'Filters cleared');
    }

    public function applyFilters()
    {
        foreach ($this->filters as $key => $filter) {
            if ($filter == null || $filter == '') {
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
