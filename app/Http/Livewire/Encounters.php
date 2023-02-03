<?php

namespace App\Http\Livewire;

use App\Data\FiltersData;
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

    public FiltersData $filters;

    public string $search = '';

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
        $matches_data = $this->me->getMatchesCache($this->filters);
        $encounters = $matches_data['encounters'];

        if ($this->search) {
            $summonerIds = Summoner::where('name', 'like', '%'.$this->search.'%')->pluck('id');
            $encounters = $encounters->filter(function ($total, $key) use ($summonerIds) {
                return $summonerIds->contains($key);
            });
        }
        $encounters = $encounters->sortBy(fn ($summoner) => $summoner, SORT_REGULAR, true);
        $encountersPaginate = $encounters->forPage($this->page, $this->perPage);
        $summoners = Summoner::whereIn('id', $encountersPaginate->keys())->select(['id', 'name'])->get()->each(function (Summoner $summoner) use ($encountersPaginate) {
            $summoner->total = $encountersPaginate->get($summoner->id);
        })->sortBy(fn ($summoner) => $summoner->total, SORT_REGULAR, true);

        $paginator = new LengthAwarePaginator($summoners, $encounters->count(), $this->perPage, $this->page);

        return view('livewire.encounters', ['encounters' => $paginator]);
    }
}
