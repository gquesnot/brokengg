<?php

namespace App\Http\Livewire;

use App\Models\Champion as ChampionModel;
use App\Models\Map;
use App\Models\Mode;
use App\Models\Queue;
use App\Models\Summoner;
use App\Models\Summoner as SummonerModel;

use App\Traits\PaginateTrait;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class LiveGame extends Component
{
    use PaginateTrait;

    public $me;

    public $info;

    public $version;

    public $loaded = false;

    public $lobbyLoaded = false;

    public ?Collection $lobbyParticipants = null;

    public $myTeam = 100;

    public ?string $search = null;

    public ?Collection $participants = null;

    public function mount(Summoner $me, $version)
    {
        $this->fill([
            "me" => $me,
            "version" => $version,
        ]);
        $this->getLiveGame();
    }

    public function getLiveGame()
    {
        $riotApi  = new \App\Helpers\RiotApi();
        # TODO MAKE AGAIN
        $data = $riotApi->getSummonerLiveGame($this->me);
        $this->loaded = $data != null;
        if (! $this->loaded) {
            return;
        }
        $this->participants = collect($data->participants);
        $encountersMatchIds = $this->me->getCachedMatchesQuery([]);
        $encounters = $this->me->getCachedEncounters($encountersMatchIds, []);
        $this->participants = $this->participants->map(function ($participant) use ($encounters) {
            $participant->total = 0;
            $summoner = Summoner::where('summoner_id', $participant->summonerId)->first();
            if ($summoner) {
                if ($encounters->has($summoner->id)) {
                    $participant->total = $encounters->get($summoner->id);
                    $participant->id = $summoner->id;
                }
            }

            $participant->champion = ChampionModel::where('id', $participant->championId)->first();
            if ($participant->summonerId == $this->me->summoner_id) {
                $this->myTeam = $participant->teamId;
            }

            return (array) $participant;
        })->map(function ($participant) {
            $participant['vs'] = $this->myTeam != $participant['teamId'];

            return $participant;
        });

        $this->info = [
            'queue' => Queue::where('id', $data->gameQueueConfigId)->first(),
            'map' => Map::where('id', $data->mapId)->first(),
            'mode' => Mode::where('name', $data->gameMode)->first(),
            'duration' => Carbon::createFromTimestamp($data->gameStartTime / 1000)->diff(Carbon::now())->format('%H:%I:%S'),
        ];
    }

    public function searchSummoners()
    {

        if ($this->search == null || $this->search == '') {
            $this->lobbyParticipants = null;

            return;
        }
        $riotApi = new \App\Helpers\RiotApi();
        $encountersMatchIds = $this->me->getCachedMatchesQuery([]);
        $encounters = $this->me->getCachedEncounters($encountersMatchIds, []);
        $this->lobbyParticipants = collect(explode("\n", $this->search))->map(function ($name) use ($encounters, $riotApi) {
            if (str_contains($name, 'joined the lobby')) {
                $name = str_replace(' joined the lobby', '', $name);
            }

            $summoner = SummonerModel::where('name', $name)->first();
            if (!$summoner){
                $summoner = $riotApi->getAndUpdateSummonerByName($name);
            }
            if (!$summoner){
                return null;
            }

            $summoner->total = 0;
            if ($encounters->has($summoner->id)) {
                $summoner->total = $encounters->get($summoner->id);
            }

            return $summoner;
        })->filter(function ($summoner) {
            return $summoner != null;
        });
        $this->lobbyLoaded = true;
    }

    public function showVersus($id)
    {
        $this->emitUp('showVersus', $id);
    }

    public function render()
    {
        return view('livewire.live-game');
    }
}
