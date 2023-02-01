<?php

namespace App\Http\Livewire;

use App\Data\FiltersData;
use App\Enums\FLashEnum;
use App\Helpers\RiotClientApi;
use App\Helpers\Stats;
use App\Models\Summoner;
use App\Traits\PaginateTrait;
use App\Traits\QueryParamsTrait;
use Livewire\Component;
use Symfony\Component\Process\Process;

class SummonerVersus extends Component
{
    use PaginateTrait;
    use QueryParamsTrait;

    public Summoner $me;

    public $other;

    public $version;

    public string $versus_text = '';

    public array $versus_text_exploded = [];

    public $with = 'with';

    public $withOptions = [
        'with' => 'With',
        'vs' => 'Versus',
    ];

    public FiltersData $filters;

    public function mount(Summoner $me, $version, FiltersData $filters, ?Summoner $other = null)
    {
        $this->fill([
            'me' => $me,
            'other' => $other,
            'version' => $version,
            'filters' => $filters,
        ]);
    }

    public function getVersusWithOrVs($details, $me_stat, $other_stat)
    {
        $result = [];
        if (count($details) == 0) {
            return $result;
        }
        $result[] = 'KDA: '.$me_stat->kda.' vs '.$other_stat->kda;
        $result[] = 'Winrate: '.$me_stat->win_percent.'% vs '.$other_stat->win_percent.'%';
        $result[] = 'Kill participation: '.$me_stat->kill_participation.'% vs '.$other_stat->kill_participation.'%';
        $result[] = 'Avg Score: '."{$me_stat->avg_kills}/{$me_stat->avg_deaths}/{$me_stat->avg_assists}".' vs '."{$other_stat->avg_kills}/{$other_stat->avg_deaths}/{$other_stat->avg_assists}";
        $result[] = 'Avg kda: '.$me_stat->kda.' vs '.$other_stat->kda;
        $result[] = 'Games played: '.$me_stat->game_played;
        foreach ($details as $detail) {
            $result[] = $detail->sinceMatchEnd().' '.$detail->me->champion->name.' '.$detail->me->kda.' vs '.$detail->other->kda.' '.$detail->other->champion->name;
        }

        return $result;
    }

    public function setVersusText($allDetails, $stats)
    {
        $versus_text = [];
        $this->versus_text = '';
        $with_details = $this->filterDetails($allDetails, 'with');
        $with_stats_me = new Stats($with_details->pluck('me'));
        $with_stats_other = new Stats($with_details->pluck('other'));
        $versus_text[] = $this->me->name.' with '.$this->other->name;
        $versus_text = array_merge($versus_text, $this->getVersusWithOrVs($with_details, $with_stats_me, $with_stats_other));
        $versus_text[] = ' ';
        $vs_details = $this->filterDetails($allDetails, 'vs');
        $vs_stats_me = new Stats($vs_details->pluck('me'));
        $vs_stats_other = new Stats($vs_details->pluck('other'));
        $versus_text[] = $this->me->name.' vs '.$this->other->name;
        $versus_text = array_merge($versus_text, $this->getVersusWithOrVs($vs_details, $vs_stats_me, $vs_stats_other));
        // merge versus text to create array of 200 chars line length
        $result = [];
        $count = 0;
        $tmp = [];
        for ($i = 0; $i < count($versus_text); $i++) {
            if ($count + strlen($versus_text[$i]) > 200) {
                $result[] = implode("\n", $tmp);
                $tmp = [];
                $count = 0;
            }
            $tmp[] = $versus_text[$i];
            $count += strlen($versus_text[$i]);
        }
        $result[] = implode("\n", $tmp);
        $this->versus_text_exploded = $result;
        $this->versus_text = implode("\n\n", $result);
    }

    public function filterDetails($details, $with = 'with')
    {
        return $details->filter(function ($detail) use ($with) {
            return ($detail->me->won == $detail->other->won) == ($with == 'with');
        });
    }

    public function newProcess(...$args): Process
    {
        return new Process($args);
    }

    public function sendOtherMessage()
    {
        $client = new RiotClientApi();
        if (! $client->is_ok) {
            return;
        }

        foreach ($this->versus_text_exploded as $text) {
            $client->post_other_conversation($this->other->name, $text);
        }
    }

    public function sendChampSelectMessage()
    {
        $client = new RiotClientApi();
        if (! $client->is_ok) {
            $this->emitTo(BaseSummoner::class, 'flashMessage', FLashEnum::ERROR->value, 'Error connecting to Riot Client API');

            return;
        }
        $conversations = collect($client->get_conversations()->json());

        $champSelectComp = $conversations->filter(function ($conversation) {
            return $conversation['type'] == 'championSelect' || $conversation['type'] == 'customGame';
        })->first();
        if ($champSelectComp != null) {
            foreach ($this->versus_text_exploded as $text) {
                $response = $client->post_champ_select_conversation($champSelectComp['id'], $text);
            }
        } else {
            $this->emitTo(BaseSummoner::class, 'flashMessage', FLashEnum::ERROR->value, 'No champ select conversation found');
        }
    }

    public function render()
    {
        $stats = ['me' => null, 'other' => null];
        if ($this->other != null) {
            $allDetails = $this->me->versus($this->other, $this->filters);
            $details = $this->filterDetails($allDetails, $this->with);
            $stats['me'] = new Stats($details->pluck('me'));
            $stats['other'] = new Stats($details->pluck('other'));
        } else {
            $allDetails = $details = collect([]);
        }

        $this->setVersusText($allDetails, $stats);

        return view(
            'livewire.summoner-versus',
            [
                'details' => $this->paginate($details),
                'stats' => $stats,
            ]
        );
    }
}
