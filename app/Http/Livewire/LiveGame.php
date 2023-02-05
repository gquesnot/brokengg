<?php

namespace App\Http\Livewire;

use App\Data\FiltersData;
use App\Models\Champion as ChampionModel;
use App\Models\Summoner;
use App\Models\Summoner as SummonerModel;
use App\Traits\PaginateTrait;
use Illuminate\Support\Collection;
use Livewire\Component;

class LiveGame extends Component
{
    use PaginateTrait;

    public Summoner $me;

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
            'me' => $me,
            'version' => $version,
        ]);
        $this->loadLiveGame();
    }

    public function loadLiveGame()
    {
        $live_game_data = $this->me->getLiveGame();
        if ($live_game_data == null) {
            $this->loaded = false;

            return;
        }

        $this->participants = collect($live_game_data['participants']);
        $encounters = $this->me->getMatchesCache(FiltersData::from([]))['encounters'];
        $this->participants = $this->participants->map(function ($participant) use ($encounters) {
            $participant['total'] = 0;
            $summoner = Summoner::where('summoner_id', $participant['summonerId'])->first();
            if ($summoner) {
                if ($encounters->has($summoner->id)) {
                    $participant['total'] = $encounters->get($summoner->id);
                    $participant['id'] = $summoner->id;
                }
            }

            $participant['champion'] = ChampionModel::where('id', $participant['championId'])->first();
            if ($participant['summonerId'] == $this->me->summoner_id) {
                $this->myTeam = $participant['teamId'];
            }

            return (array) $participant;
        })->map(function ($participant) {
            $participant['vs'] = $this->myTeam != $participant['teamId'];

            return $participant;
        });
        $this->loaded = true;

        $this->info = $live_game_data['info'];
    }

    public function searchSummoners()
    {
        if ($this->search == null || $this->search == '') {
            $this->lobbyParticipants = null;

            return;
        }
        $encounters = $this->me->getMatchesCache(FiltersData::from([]))['encounters'];
        $this->lobbyParticipants = collect(explode("\n", $this->search))->map(function ($name) use ($encounters) {
            if (str_contains($name, 'joined the lobby')) {
                $name = str_replace(' joined the lobby', '', $name);
            }
            $summoner = SummonerModel::updateOrCreateByName($name);

            if (! $summoner) {
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
