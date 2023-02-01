<?php

namespace App\Http\Controllers;

use App\Data\champion\ChampionStats;
use App\Data\item\ItemMythicStats;
use App\Data\item\ItemStats;
use App\Models\Champion;
use App\Models\Item;
use App\Models\Map;
use App\Models\Mode;
use App\Models\Queue;
use App\Models\Version;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;

class SyncLolController extends Controller
{
    public $client;

    private array $wikStatsMapping = [
        ' ability haste' => 'ah',
        ' ability power' => 'ap',
        '% bonus movement speed' => 'msPercent',
        '% magic penetration' => 'magicPenPercent',
        '% armor penetration' => 'armorPenPercent',
        '% bonus attack speed' => 'asPercent',
        '% omnivamp' => 'omnivamp',
        ' armor' => 'armor',
        ' lethality' => 'lethality',
        ' magic penetration' => 'magicPen',
        ' magic resistance' => 'mr',
        ' bonus movement speed' => 'ms',
        ' bonus health' => 'hp',
        ' tenacity' => 'tenacity',
        ' slow resistance' => 'slowResistance',
        ' bonus attack damage' => 'ad',

    ];

    public function index()
    {
        $version = $this->syncVersion();
        $this->syncChampions($version->name);
        $this->syncItems($version->name);
        //dd($this->getSummoner('random iron'));
        $this->syncModes();
        $this->syncMaps();
        //$this->syncTypes();
        $this->syncQueues();
        #$this->downloadJsonsChampionsItems();
    }

    private function syncVersion()
    {
        $versions = Http::withoutVerifying()->get('https://ddragon.leagueoflegends.com/api/versions.json')->json();
        $lastVersion = $versions[0];
        $version = Version::firstOrCreate([
            'name' => $lastVersion,
        ]);

        return $version;
    }

    private function syncChampions($version)
    {
        $champions = Http::withoutVerifying()->get('https://ddragon.leagueoflegends.com/cdn/'.$version.'/data/en_US/champion.json')->json();
        foreach ($champions['data'] as $championName => $champion) {
            $champion_detail = Http::withoutVerifying()->get('https://ddragon.leagueoflegends.com/cdn/'.$version.'/data/en_US/champion/'.$championName.'.json')->json()['data'][$championName];
            $stats = ChampionStats::mapping($champion_detail['stats']);
            $champion_db = Champion::find($champion['key']);

            if (! $champion_db) {
                $champion_db = Champion::create([
                    'id' => $champion['key'],
                    'name' => $championName,
                    'title' => $champion['title'],
                    'img_url' => $champion['image']['full'],
                    'stats' => $stats,
                    'champion_id' => $champion['id'],
                ]);
            } else {
                $champion_db->update([
                    'name' => $championName,
                    'title' => $champion['title'],
                    'img_url' => $champion['image']['full'],
                    'stats' => $stats,
                    'champion_id' => $champion['id'],
                ]);
            }
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
        $items = Http::withoutVerifying()->get('https://ddragon.leagueoflegends.com/cdn/'.$version.'/data/en_US/item.json')->json();
        $stats_keys = [];
        foreach ($items['data'] as $itemId => $item) {
            $stats = [];
            if (str_contains($item['description'], 'ornnBonus')) {
                continue;
            }
            if (count($item['stats']) > 0 && $item['description'] != '') {
                $stats = $this->parseXmlDescription($item['description']);
                if ($itemId === '4645') {
                    $stats_details['magic penetration'] = 15;
                }
            }
            $stats_keys = array_merge($stats_keys, array_keys($stats));
            $gold = $item['gold']['total'];
            if ($gold == 0) {
                continue;
            }
            $itemDb = Item::find($itemId);
            $stats = ItemStats::mapping($stats);

            if ($itemDb) {
                $itemDb->update([
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'tags' => $item['tags'],
                    'gold' => $item['gold']['total'],
                    'stats' => $stats,
                    'mythic_stats' => null,
                    'colloq' => $item['colloq'],
                    'img_url' => $item['image']['full'],
                ]);
            } else {
                $itemDb = Item::create([
                    'id' => $itemId,
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'tags' => $item['tags'],
                    'gold' => $item['gold']['total'],
                    'stats' => $stats,
                    'mythic_stats' => null,
                    'colloq' => $item['colloq'],
                    'img_url' => $item['image']['full'],
                ]);
            }
        }
        // dd(array_unique($stats_keys));
        $this->getItemsTypeFromLolFandom();
        $this->getStatsOfMythicItems();
    }

    private function syncModes()
    {
        $modes = Http::withoutVerifying()->get('https://static.developer.riotgames.com/docs/lol/gameModes.json')->json();
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
            'name' => $mode['gameMode'],
            'description' => str_replace('games', '', $mode['description']),
        ]);
    }

    private function syncMaps()
    {
        $maps = Http::withoutVerifying()->get('https://static.developer.riotgames.com/docs/lol/maps.json')->json();
        foreach ($maps as $map) {
            $tmp = Map::firstOrCreate([
                'id' => $map['mapId'],
                'name' => $map['mapName'],
                'description' => $map['notes'],
            ]);
        }
    }

    private function syncQueues()
    {
        $queues = Http::withoutVerifying()->get('https://static.developer.riotgames.com/docs/lol/queues.json')->json();
        foreach ($queues as $queue) {
            Queue::firstOrCreate([
                'id' => $queue['queueId'],
                'map' => $queue['map'],
                'description' => $queue['description'] ?? '',
            ]);
        }
    }

    public function parseXmlDescription($description)
    {
        $ori = $description;
        $description = str_replace('<br>', '', $description);
        $description = str_replace('<li>', '', $description);
        // cut at first <stats> and last </stats>
        $description = substr($description, strpos($description, '<stats>') + 7, strrpos($description, '</stats>') - strpos($description, '<stats>') - 7);

        // remove first <attention>
        $description = substr($description, strpos($description, '<attention>') + 11);
        $descriptions = explode('<attention>', $description);
        $result = [];
        foreach ($descriptions as $tmp_description) {
            if ($tmp_description == '') {
                continue;
            }
            $tmp_description = explode('</attention>', $tmp_description);
            if (count($tmp_description) != 2) {
                dd($descriptions, $ori);
            }
            $name = trim($tmp_description[1]);
            $value = $tmp_description[0];
            if (str_contains($value, '%')) {
                $value = str_replace('%', '', $value);
                $name = $name.' Percent';
            }

            $result[strtolower($name)] = $value;
        }

        return $result;
    }

    public function getItemsTypeFromLolFandom()
    {
        $legendary = Http::withoutVerifying()->get('https://leagueoflegends.fandom.com/wiki/Template:Items/List')->body();
        $dom = HtmlDomParser::str_get_html($legendary);
        $root = $dom->findOne('#items #grid #item-grid #grid #item-grid');
        $categoryNames = $root->find('dt');
        $allItems = $root->find('.tlist');
        $itemsCateogry = [];
        for ($i = 0; $i < count($categoryNames); $i++) {
            $categoryName = $categoryNames[$i]->text();
            $itemsList = $allItems[$i]->find('.item-icon');
            $itemsName = [];
            foreach ($itemsList as $item) {
                //value to utf8
                $itemsName[] = htmlspecialchars_decode($item->getAttribute('data-item'), ENT_QUOTES);
            }
            $itemsCateogry[$categoryName] = $itemsName;
        }
        foreach ($itemsCateogry as $categoryName => $category) {
            $categoryName = mb_strtolower(str_replace(' ', '_', trim(str_replace('items', '', $categoryName))));
            if (str_contains($categoryName, 'ornn')) {
                $categoryName = 'mythic';
            }
            foreach ($category as $itemName) {
                //fix mapping
                // gold from pyke
                if ($itemName == "'Your Cut'") {
                    $itemName = 'Your Cut';
                } //magic boots
                elseif ($itemName == 'Slightly Magical Boots') {
                    $itemName = 'Slightly Magical Footwear';
                }
                $items = Item::where('name', $itemName)->get();
                if (count($items) == 0) {
                    $items = Item::where('name', 'LIKE', "%$itemName%")->get();
                }
                if (count($items)) {
                    foreach ($items as $item) {
                        $item->type = mb_strtolower($categoryName);
                        $item->save();
                    }
                }
            }
        }
    }

    public function getStatsOfMythicItems()
    {
        $items = Item::whereType('mythic')->get();
        $res = [];
        foreach ($items as $item) {
            $url = "https://leagueoflegends.fandom.com/api.php?format=json&action=parse&disablelimitreport=true&prop=text&title=List_of_items&text={{Tooltip/Item|item=$item->name|enchantment=|variant=|game=lol}}";
            $datas = Http::withoutVerifying()->get($url)->json()['parse']['text']['*'];
            $mythicStats = $this->findMythicStatsInHtml($item, $datas);
            $res = array_merge($res, array_keys($mythicStats));
            // $stats = $item->stats_description;
            $item->mythic_stats = ItemMythicStats::mapping($mythicStats);
            $item->save();
        }
    }

    public function findMythicStatsInHtml($item, $datas)
    {
        $dom = HtmlDomParser::str_get_html($datas);
        $rows = $dom->find('table tr');
        $res = [];
        foreach ($rows as $row) {
            if ($row->childNodes()[0]->text() == 'Mythic:') {
                // use regex pattern to find all stats like number% or number + stat (can be 1 or 2 words)
                // exemple : 10% Attack Speed => "Attack speed" = 10
                // exemple : 200 Health => "Health" = 200
                $tmp = Str::of($row->childNodes()[1]->text())
                    ->remove('Empowers each of your other Legendary items with ')
                    ->remove('.')
                    ->remove('and ')
                    ->explode(', ');
                foreach ($tmp as $stat) {
                    $stat = Str::of($stat)->explode(' ', 2)->each(function ($item) {
                        return trim($item);
                    });
                    if (count($stat) == 2) {
                        $key = $stat[1];
                        $value = $stat[0];

                        $key = Str::of($key)->remove('bonus ')->explode(' ')->take(2)->implode('_');
                        if (Str::of($key)->contains('%')) {
                            // remove last word
                            $keys = Str::of($key)->explode('_');
                            $key = $keys->take(count($keys) - 1)->implode('_');
                        }
                        if (str_contains($value, '%')) {
                            $value = str_replace('%', '', $value);
                            $key = $key.'_percent';
                        }

                        // take 2 first words of key

                        $res[$key] = $value;
                    }
                }
            }
        }

        return $res;
    }

    public function mapWikiKeys($stat)
    {
        foreach ($this->wikStatsMapping as $key => $value) {
            if (str_contains($stat, $key)) {
                return ['str' => $key, 'key' => $value];
            }
        }

        return null;
    }

    public function parseHtmlTag($text, $htmlTag)
    {
        $originalText = $text;
        $text = explode("</${htmlTag}>", $text)[0];
        $text = explode("<${htmlTag}>", $text);
        if (count($text) !== 2) {
            //dd($text, $originalText);
            return '';
        }

        return $text[1];
    }

    public function downloadJsonsChampionsItems()
    {
        foreach (Champion::all() as $champion){
            $response = Http::withoutVerifying()
                ->get("http://cdn.merakianalytics.com/riot/lol/resources/latest/en-US/champions/{$champion->name}.json");
            Storage::put("champions/{$champion->name}.json", $response->body());

        }
        $response = Http::withoutVerifying()
            ->get("http://cdn.merakianalytics.com/riot/lol/resources/latest/en-US/champions.json");
        Storage::put("champions.json", $response->body());
    }
    private function downloadJsonItems(){
        $response = Http::withoutVerifying()
            ->get("http://cdn.merakianalytics.com/riot/lol/resources/latest/en-US/items.json");
        Storage::put("items.json", $response->body());
    }
}
