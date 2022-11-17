<?php

namespace App\Http\Livewire;

use App\Enums\FLashEnum;
use App\Helpers\RiotApi;
use App\Jobs\AutoUpdateJob;
use App\Jobs\UpdateMatchesJob;
use App\Jobs\UpdateMatchJob;
use App\Models\Summoner as SummonerModel;
use App\Models\Version;
use App\Traits\FlashTrait;
use App\Traits\QueryParamsTrait;
use Bus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;

class BaseSummoner extends Component
{
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

    protected $listeners = ['filtersUpdated', 'flashMessage'];

    protected $queryString = [
        'filters',
    ];

    public function mount($summonerId, $otherSummonerId = null)
    {
        $this->summonerId = $summonerId;
        $this->otherSummonerId = $otherSummonerId;
        # TODO: check user has all account_apis
        $this->version = Version::orderBy('created_at')->first()->name;
        $this->summoner = SummonerModel::find($summonerId);
        if (!$this->summoner){
            return redirect()->route('home');
        }
        if (!$this->summoner->complete) {
            $riotApi = new RiotApi();
            $summoner = $riotApi->getAndUpdateSummonerByName($this->summoner->name);
        }
        $this->tab = Route::currentRouteName();
        $this->setFilters();
    }

    public function flashMessage( string $type, string $message)
    {
        Session::flash($type, $message);
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

    public function fullUpdateSummoner(){
        $this->summoner->last_scanned_match = null;
        $this->summoner->save();
        $this->updateSummoner();
    }

    public function updateSummoner()
    {
        #UpdateMatchJob::dispatchSync($this->summoner);
//        UpdateMatchesJob::dispatchSync();
        Bus::chain([
            new UpdateMatchJob($this->summoner),
            new UpdateMatchesJob(),
        ])->dispatch();

        Session::flash('success', 'Summoner updating ...');
    }

    public function render()
    {
        return view('livewire.base-summoner');
    }
}
