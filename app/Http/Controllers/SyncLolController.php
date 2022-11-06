<?php

namespace App\Http\Controllers;

use App\Models\Champion;
use App\Models\Item;
use App\Models\Map;
use App\Models\Mode;
use App\Models\Queue;
use App\Models\Version;


class SyncLolController extends Controller
{
    public  $client;

    public function index()
    {
        $this->client = new \GuzzleHttp\Client();


        $version = $this->syncVersion();
        $this->syncChampions($version->name);
        $this->syncItems($version->name);
        //dd($this->getSummoner('random iron'));
        $this->syncModes();
        $this->syncMaps();
        //$this->syncTypes();
        $this->syncQueues();
    }

    private function syncVersion()
    {
        $versions = json_decode($this->client->request('GET', 'https://ddragon.leagueoflegends.com/api/versions.json')->getBody()->getContents());
        $lastVersion = $versions[0];
        $version = Version::firstOrCreate([
            'name' => $lastVersion,
        ]);

        return $version;
    }

    private function syncChampions($version)
    {
        $champions = json_decode($this->client->request('GET', 'https://ddragon.leagueoflegends.com/cdn/'.$version.'/data/en_US/champion.json')->getBody()->getContents());
        foreach ($champions->data as $championName => $champion) {
            Champion::firstOrCreate([
                'name' => $championName,
                'champion_id' => $champion->id,
                'id' => intval($champion->key),
                'title' => $champion->title,
                'img_url' => $champion->image->full,
            ]);
        }
    }

//    public function syncTypes()
//    {
//        $types = json_decode($this->client->request('GET', 'https://static.developer.riotgames.com/docs/lol/gameTypes.json')->getBody()->getContents());
//        foreach ($types as $type) {
//            Types::firstOrCreate([
//                "name" => $type->gametype,
//                "description" => $type->description,
//            ]);
//        }
//    }

    private function syncItems($version)
    {
        $items = json_decode($this->client->request('GET', 'https://ddragon.leagueoflegends.com/cdn/'.$version.'/data/en_US/item.json')->getBody()->getContents());
        foreach ($items->data as $itemId => $item) {
            Item::firstOrCreate([
                'name' => $item->name,
                'id' => $itemId,
                'description' => $item->description,
                'img_url' => $item->image->full,
            ]);
        }
    }

    private function syncModes()
    {
        $modes = json_decode($this->client->request('GET', 'https://static.developer.riotgames.com/docs/lol/gameModes.json')->getBody()->getContents());
        foreach ($modes as $mode) {
            $this->getFirstOrCreate($mode);
        }
    }

    /**
     * @param  mixed  $mode
     * @return void
     */
    private function getFirstOrCreate(mixed $mode): void
    {
        Mode::firstOrCreate([
            'name' => $mode->gameMode,
            'description' => str_replace('games', '', $mode->description),
        ]);
    }

    private function syncMaps()
    {
        $maps = json_decode($this->client->request('GET', 'https://static.developer.riotgames.com/docs/lol/maps.json')->getBody()->getContents());
        foreach ($maps as $map) {
            Map::firstOrCreate([
                'id' => $map->mapId,
                'name' => $map->mapName,
                'description' => $map->notes,
            ]);
        }
    }

    private function syncQueues()
    {
        $queues = json_decode($this->client->request('GET', 'https://static.developer.riotgames.com/docs/lol/queues.json')->getBody()->getContents());
        foreach ($queues as $queue) {
            Queue::firstOrCreate([
                'id' => $queue->queueId,
                'map' => $queue->map,
                'description' => $queue->description ?? '',
            ]);
        }
    }
}
