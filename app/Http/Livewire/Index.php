<?php

namespace App\Http\Livewire;

use App\Enums\TabEnum;
use App\Helpers\RiotApi;
use App\Http\Controllers\SyncLolController;
use App\Jobs\UpdateRiotKeysJob;
use App\Models\ApiAccount;
use App\Models\Summoner;
use App\Traits\FlashTrait;

use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Index extends Component
{
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
        $riotApi = new RiotApi();

        $summoner = $riotApi->getAndUpdateSummonerByName($this->summonerName);
        return redirect()->route(TabEnum::MATCHES->value, ['summonerId' => $summoner->id]);
    }

    public function render()
    {
        return view('livewire.index');
    }
}
