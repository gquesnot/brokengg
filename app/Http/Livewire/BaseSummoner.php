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

    public TabEnum $tab;

    public ?int $other = null;

    public string $version = '';

    public FiltersData $filters;

    public array $summonerToOpen = [];

    public ?int $otherSummonerId = 0;

    public int $summonerId = 0;

    public ?int $matchId = 0;

    protected $listeners = ['updateFilters', 'flashMessage'];

    public ?string $date_start = null;

    public ?string $date_end = null;

    public ?int $champion = null;

    public ?int $queue = null;

    public ?bool $filter_encounters = null;

    protected $queryString = [
        'date_start',
        'date_end',
        'champion',
        'queue',
        'filter_encounters',
    ];

    public function mount(int $summonerId, ?int $otherSummonerId = null, ?int $matchId = null)
    {
        $this->filters = new FiltersData(
            $this->date_start,
            $this->date_end,
            $this->champion,
            $this->queue,
            $this->filter_encounters
        );
        $this->fill([
            'summonerId' => $summonerId,
            'otherSummonerId' => $otherSummonerId,
            'matchId' => $matchId,
        ]);
        // TODO: check user has all account_apis
        $this->version = Version::orderByDesc('id')->first()->name;
        $this->summoner = SummonerModel::find($summonerId);
        if (! $this->summoner) {
            return redirect()->route('home');
        }
        if (! $this->summoner->complete) {
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
        $this->summoner->auto_update = ! $this->summoner->auto_update;
        $this->summoner->save();
    }

    public function updateFilters($filters)
    {
        $this->filters = FiltersData::withoutMagicalCreationFrom($filters);
        $this->champion = $this->filters->champion;
        $this->queue = $this->filters->queue;
        $this->date_start = $this->filters->date_start;
        $this->date_end = $this->filters->date_end;
        $this->filter_encounters = $this->filters->filter_encounters;
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
        //UpdateMatchJob::dispatchSync($this->summoner);
        //UpdateMatchesJob::dispatchSync();
        Bus::chain([
            new UpdateMatchJob($this->summoner),
            new UpdateMatchesJob(),
        ])->dispatch();

        Session::flash('success', 'Summoner updating ...');
    }

    public function render()
    {
        return view('livewire.base-summoner', ['filters' => FiltersData::fromLivewire($this->filters ?? [])]);
    }
}
