<?php

namespace App\Http\Livewire;

use App\Data\FiltersData;
use App\Enums\TabEnum;
use App\Exceptions\RiotApiForbiddenException;
use App\Jobs\UpdateMatchesJob;
use App\Models\Summoner as SummonerModel;
use App\Models\Version;
use App\Traits\FlashTrait;
use App\Traits\QueryParamsTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->version = Version::orderByDesc('id')->first()->name;
        $this->summoner = SummonerModel::with('leagues')->find($summonerId);
        if (! $this->summoner) {
            return redirect()->route('home');
        }
        $this->summoner->append('best_league');
        try {
            $this->summoner->selfUpdate();
        } catch (RiotApiForbiddenException $e) {
            Log::error('RiotApiForbiddenException: '.$e->getMessage());
        }
        $this->tab = TabEnum::from(Route::currentRouteName());
    }

    public function flashMessage(string $type, string $message)
    {
        Session::flash($type, $message);
    }

    public function toggleAutoUpdate()
    {
        Log::info('Toggle auto update for '.$this->summoner->name.' by '.request()->ip());
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

    public function updateSummoner(bool $full = false)
    {
        $executed = RateLimiter::attempt(
            'updateSummoner_'.request()->ip(),
            1,
            function () use ($full) {
                if ($full) {
                    $this->summoner->last_scanned_match = null;
                    $this->summoner->save();
                }
                Log::info('Updating matches for '.$this->summoner->name.' by '.request()->ip());
                UpdateMatchesJob::dispatch($this->summoner->id);
                Session::flash('success', 'Summoner updating ...');
            },
            10
        );
        if (! $executed) {
            Session::flash('error', 'You can only update a summoner every 10 seconds');
        }
    }

    public function render()
    {
        return view('livewire.base-summoner', ['filters' => FiltersData::fromLivewire($this->filters ?? [])]);
    }
}
