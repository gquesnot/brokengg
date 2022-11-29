<?php

namespace App\Http\Livewire;

use App\Helpers\RiotApi;
use App\Models\Item;
use App\Models\Matche;
use App\Models\Summoner;
use Illuminate\Support\Collection;
use Livewire\Component;

class MatchDetail extends Component
{


    public Summoner $me;
    public Matche $match;
    public string $version;

    public function mount(Summoner $me, int $matchId, string $version)
    {
        $this->fill([
            "me" => $me,
            'version' => $version,
            "match" => Matche::find($matchId),
        ]);

    }


    public function getItems(): Collection
    {
        return Item::all()->filter(function (Item $item) {
            if (count($item->tags) == 0) {
                return false;
            }
            return !in_array('Consumable', $item->tags)
                && !in_array('Trinket', $item->tags)
                && !in_array('Jungle', $item->tags)
                && !in_array('Active', $item->tags);
        })->keyBy('id');

    }

    public function render()
    {
        $api = new RiotApi();
        $participants = $api->getMatchTimeline($this->match);
        $participant_idx = $participants->first(function ($participant) {
            return $participant['puuid'] == $this->me->puuid;
        })['id'];
        $items = $this->getItems();
        //dd($items->first()->stats);
        return view('livewire.match-detail', [
            "participants" => $participants,
            'items' => $items->toArray(),
            'participant_idx' => $participant_idx,
        ]);
    }
}
