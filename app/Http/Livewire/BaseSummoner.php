<?php

namespace App\Http\Livewire;

use App\Data\FiltersData;
use App\Enums\TabEnum;
use App\Helpers\RiotApi;
use App\Jobs\UpdateMatchesJob;
use App\Jobs\UpdateMatchJob;
use App\Models\Summoner as SummonerModel;
use App\Models\Version;
use App\Traits\FlashTrait;
use App\Traits\QueryParamsTrait;
use Bus;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;
use function PHPUnit\Framework\isInstanceOf;

class BaseSummoner extends Component
{
    use FlashTrait;
    use WithPagination;
    use QueryParamsTrait;

    public ?SummonerModel $summoner = null;

    public TabEnum $tab;

    public ?int $other = null;

    public string $version = '';

    public FiltersData $filters;


    public array $summonerToOpen = [];

    public ?int $otherSummonerId = 0;

    public int $summonerId = 0;
    public ?int $matchId = 0;

    protected $listeners = ['filtersUpdated', 'flashMessage'];

    protected $queryString = [
        'filters'
    ];


    public function boot(){
        $filters = request()->query('filters');
        $this->filters =  FiltersData::from($filters ?? []);
        if (!$filters){
            request()->query->remove('filters');
        }

    }

    public function mount(int $summonerId, ?int $otherSummonerId = null, ?int $matchId=null)
    {

        $this->fill([
            "summonerId" => $summonerId,
            "otherSummonerId" => $otherSummonerId,
            "matchId" => $matchId,
        ]);
        # TODO: check user has all account_apis
        $this->version = Version::orderByDesc('id')->first()->name;
        $this->summoner = SummonerModel::find($summonerId);
        if (!$this->summoner) {
            return redirect()->route('home');
        }
        if (!$this->summoner->complete) {
            $riotApi = new RiotApi();
            $summoner = $riotApi->getAndUpdateSummonerByName($this->summoner->name);
        }
        $this->tab = TabEnum::from(Route::currentRouteName());
    }


    public function flashMessage(string $type, string $message)
    {
        Session::flash($type, $message);
    }



    public function toggleAutoUpdate()
    {
        $this->summoner->auto_update = !$this->summoner->auto_update;
        $this->summoner->save();
    }

    public function filtersUpdated(array $filters)
    {

        $this->filters = FiltersData::from($filters);
    }

    public function loadEncounter($encounterId)
    {
        if (in_array($encounterId, $this->summonerToOpen)) {
            unset($this->summonerToOpen[array_search($encounterId, $this->summonerToOpen)]);
        } else {
            $this->summonerToOpen[] = $encounterId;
        }
    }

    public function fullUpdateSummoner()
    {
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
