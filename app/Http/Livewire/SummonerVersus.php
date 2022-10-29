<?php

namespace App\Http\Livewire;

use App\Traits\PaginateTrait;
use App\Traits\QueryParamsTrait;
use Livewire\Component;

class SummonerVersus extends Component
{
    use PaginateTrait;
    use QueryParamsTrait;

    public $me;

    public $other;

    public $version;

    public $stats = null;

    public $with = 'with';

    public $withOptions = [
        'with' => 'With',
        'vs' => 'Versus',
    ];

    public $filters = null;

    public function mount(\App\Models\Summoner $me, $other, $version, $filters)
    {
        $this->me = $me;
        $this->version = $version;
        if ($this->other != null) {
            $this->other = \App\Models\Summoner::where('id', $other)->first();
        } else {
            $this->other = null;
        }
    }

    public function render()
    {
        if ($this->other != null) {
            $details = $this->me->versus($this->other, $this->filters)->filter(function ($detail) {
                return ($detail->me->won == $detail->other->won) == ($this->with == 'with');
            });
            $this->getStats($details);
        } else {
            $details = collect([]);
        }

        return view('livewire.summoner-versus',
            [
                'details' => $this->paginate($details),
            ]
        );
    }

    public function getStats($details)
    {
        $this->stats = [];
        $this->stats['me'] = $this->getStat($details);
        $this->stats['other'] = $this->getStat($details, false);
    }

    public function getStat($details, $isMe = true)
    {
        $tmp = [
            'kill_participation' => 0,
            'kda' => 0,
            'game_won' => 0,
            'win_percent' => 0,
            'game_lose' => 0,
            'game_played' => 0,
        ];
        $type = $isMe ? 'me' : 'other';
        if (! $details->isEmpty()) {
            foreach ($details as $match) {
                $tmp['kill_participation'] += $match->{$type}->kill_participation;
                $tmp['kda'] += $match->{$type}->kda;
                $tmp['game_won'] += $match->{$type}->won;
                $tmp['game_lose'] += ! $match->{$type}->won;
                $tmp['game_played'] += 1;
            }
            $tmp['kill_participation'] = round($tmp['kill_participation'] / $tmp['game_played'] * 100);
            $tmp['kda'] = round($tmp['kda'] / $tmp['game_played'], 2);
            $tmp['win_percent'] = round($tmp['game_won'] / $tmp['game_played'] * 100);
        }

        return $tmp;
    }
}
