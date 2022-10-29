<?php

namespace App\Http\Livewire;

use App\Http\Controllers\SyncLolController;
use App\Models\Summoner;
use App\Traits\FlashTrait;
use App\Traits\RiotApiTrait;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Index extends Component
{
    use RiotApiTrait;
    use FlashTrait;

    public string $summonerName = 'random iron';

    public $user;

    protected $rankMatching = [
        'I' => 1,
        'II' => 2,
        'III' => 3,
        'IV' => 4,
        'V' => 5,
    ];

    public function mount()
    {
        //$this->user = User::where('id',Auth::user()->id)->first();
    }

    public function sync()
    {
        $controller = new SyncLolController();
        $controller->index();
        Session::flash('success', 'Synced');
    }

    public function searchSummoner()
    {
        $summonerData = $this->getSummonerByName($this->summonerName);
        $summoner = Summoner::where('puuid', $summonerData->puuid)->first();
        if (! $summoner) {
            $summoner = Summoner::create([
                'summoner_id' => $summonerData->id,
                'account_id' => $summonerData->accountId,
                'puuid' => $summonerData->puuid,
                'name' => $summonerData->name,
                'profile_icon_id' => $summonerData->profileIconId,
                'revision_date' => $summonerData->revisionDate,
                'summoner_level' => $summonerData->summonerLevel,
            ]);
        } else {
            $summoner->update([
                'summoner_id' => $summonerData->id,
                'account_id' => $summonerData->accountId,
                'puuid' => $summonerData->puuid,
                'name' => $summonerData->name,
                'profile_icon_id' => $summonerData->profileIconId,
                'revision_date' => $summonerData->revisionDate,
                'summoner_level' => $summonerData->summonerLevel,
            ]);
        }

        return redirect()->route('summoner', ['summonerId' => $summoner->id]);
    }

    public function render()
    {
        return view('livewire.index');
    }
}
