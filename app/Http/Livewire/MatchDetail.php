<?php

namespace App\Http\Livewire;

use App\Data\item\ItemData;
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

    public bool $readyToLoad = false;

    public function mount(Summoner $me, int $matchId, string $version)
    {
        $this->fill([
            'me' => $me,
            'version' => $version,
            'match' => Matche::find($matchId),
        ]);
    }

    public function afterInit(){
        $this->readyToLoad = true;
    }

    public function getItems(): Collection
    {
        return Item::all()->filter(function (Item $item) {
            if (count($item->tags) == 0) {
                return false;
            }

            return ! in_array('Consumable', $item->tags)
                && ! in_array('Trinket', $item->tags)
                && ! in_array('Jungle', $item->tags)
                && ! in_array('Active', $item->tags);
        })->map(fn (Item $item) => ItemData::from($item))->keyBy('id');
    }

    public function render()
    {
        $participants =collect([]);
        $participant_idx = 0;
        $items =collect([]);
        if ($this->readyToLoad){
            $api = new RiotApi();
            $participants = $api->getCachedMatchTimeline($this->match);
            $participant_idx = $participants->first(function ($participant) {
                return $participant->puuid == $this->me->puuid;
            })->id;
            $items = $this->getItems();
        }


        return view('livewire.match-detail', [
            'participants' => $participants->toArray(),
            'items' => $items->toArray(),
            'participant_idx' => $participant_idx,
        ]);
    }
}
