<?php

namespace App\Http\Livewire;

use App\Models\Summoner as SummonerModel;
use App\Models\Version;
use App\Traits\FlashTrait;
use App\Traits\QueryParamsTrait;
use App\Traits\RiotApiTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;

class BaseSummoner extends Component
{
    use RiotApiTrait;
    use FlashTrait;
    use WithPagination;
    use QueryParamsTrait;

    public ?SummonerModel $summoner = null;

    public string $tab = 'matches';

    public ?int $other = null;

    public string $version = '';

    public $filters = [
        'status' => null,
        'queue' => null,
        'dateStart' => null,
        'dateEnd' => null,
        'champion' => null,
        'filterEncounters' => null,
    ];

    public array $tabs = [
        'summoner' => 'Matches',
        'champions' => 'Champions',
        'encounters' => 'Encounters',
        'versus' => 'Versus',
        'live_game' => 'Live Game',
    ];

    public array $summonerToOpen = [];

    public ?int $otherSummonerId = 0;

    public int $summonerId = 0;

    protected $listeners = ['filtersUpdated'];

    protected $queryString = [
        'filters',
    ];

    public function mount($summonerId, $otherSummonerId = null)
    {
        $this->summonerId = $summonerId;
        $this->otherSummonerId = $otherSummonerId;
        $this->version = Version::orderBy('created_at')->first()->name;
        $this->summoner = SummonerModel::where('id', $summonerId)->first();
        $this->tab = Route::currentRouteName();
        $this->setFilters();
    }

    public function setFilters()
    {
        $baseFilter = [
            'status' => null,
            'queue' => null,
            'dateStart' => null,
            'dateEnd' => null,
            'champion' => null,
            'filterEncounters' => null,
        ];
        $this->filters = array_merge($baseFilter, $this->filters);
    }

    public function toggleAutoUpdate()
    {
        $this->summoner->auto_update = ! $this->summoner->auto_update;
        $this->summoner->save();
    }

    public function filtersUpdated($filters)
    {
        foreach ($filters as $key => $value) {
            if ($key == 'dateEnd' && $value != null) {
                $this->filters[$key] = Carbon::parse($value)->addDay();
            } else {
                $this->filters[$key] = $value;
            }
        }
        $this->filters = $filters;
    }

    public function loadEncounter($encounterId)
    {
        if (in_array($encounterId, $this->summonerToOpen)) {
            unset($this->summonerToOpen[array_search($encounterId, $this->summonerToOpen)]);
        } else {
            $this->summonerToOpen[] = $encounterId;
        }
    }

    public function updateSummoner()
    {
        $this->updateSummonerMatches($this->summoner);
        Session::flash('success', 'Summoner updating ...');
    }

    public function render()
    {
        return view('livewire.base-summoner');
    }
}
