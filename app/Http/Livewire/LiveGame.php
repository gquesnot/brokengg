<?php

namespace App\Http\Livewire;

use App\Models\Champion as ChampionModel;
use App\Models\Map;
use App\Models\Mode;
use App\Models\Queue;
use App\Models\Summoner as SummonerModel;
use App\Traits\PaginateTrait;
use App\Traits\RiotApiTrait;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class LiveGame extends Component
{
    use RiotApiTrait;
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

    public function mount($me, $version)
    {
        $this->version = $version;
        $this->me = $me;
        $this->getLiveGame();
    }

    public function getLiveGame()
    {
        $data = $this->getSummonerLiveGameByAccountId($this->me->summoner_id);
        $this->loaded = $data != null;
        if (! $this->loaded) {
            return;
        }
        $this->participants = collect($data->participants);
        $encounters = $this->me->encounters();
        $this->participants = $this->participants->map(function ($participant) use ($encounters) {
            $participant->total = 0;
            $summoner = SummonerModel::where('summoner_id', $participant->summonerId)->first();
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
        $encounters = $this->me->encounters(null, true);

        $this->lobbyParticipants = collect(explode("\n", $this->search))->map(function ($name) use ($encounters) {
            if (str_contains($name, 'joined the lobby')) {
                $name = str_replace(' joined the lobby', '', $name);
            }

            $summoner = SummonerModel::where('name', $name)->first();

            if (! $summoner) {
                $summonerData = $this->getSummonerByName($name);
                $summoner = SummonerModel::create([
                    'summoner_id' => $summonerData->id,
                    'account_id' => $summonerData->accountId,
                    'puuid' => $summonerData->puuid,
                    'name' => $summonerData->name,
                    'profile_icon_id' => $summonerData->profileIconId,
                    'revision_date' => $summonerData->revisionDate,
                    'summoner_level' => $summonerData->summonerLevel,
                ]);
            }

            $summoner->total = 0;
            if ($encounters->has($summoner->id)) {
                $summoner->total = $encounters->get($summoner->id);
            }

            return $summoner;
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
