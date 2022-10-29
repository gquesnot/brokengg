<?php

namespace App\Http\Livewire;

use App\Models\Summoner;
use App\Traits\PaginateTrait;
use App\Traits\QueryParamsTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;

class Encounters extends Component
{
    use PaginateTrait;
    use QueryParamsTrait;

    public Summoner $me;

    public $version;

    public $filters = null;

    public function mount($me, $version, $filters)
    {
        $this->me = $me;
        $this->filters = $filters;
        $this->version = $version;
    }

    public function render()
    {
        $encountersMatchIds = $this->me->getCachedMatchesQuery($this->filters);
        $encounters = $this->me->getCachedEncounters($encountersMatchIds, $this->filters);
        $encounters = $encounters->sortBy(fn ($summoner) => $summoner, SORT_REGULAR, true);
        $encountersPaginate = $encounters->forPage($this->page, $this->perPage);
        $summoners = Summoner::whereIn('id', $encountersPaginate->keys())->select(['id', 'name'])->get()->each(function (Summoner $summoner) use ($encountersPaginate) {
            $summoner->total = $encountersPaginate->get($summoner->id);
        })->sortBy(fn ($summoner) => $summoner->total, SORT_REGULAR, true);
        $paginator = new LengthAwarePaginator($summoners, $encounters->count(), $this->perPage, $this->page);

        return view('livewire.encounters', ['encounters' => $paginator]);
    }
}
