<?php

namespace App\Http\Livewire;

use App\Helpers\Stats;
use App\Models\Summoner;
use App\Traits\PaginateTrait;
use App\Traits\QueryParamsTrait;
use Livewire\Component;

class SummonerVersus extends Component
{
    use PaginateTrait;
    use QueryParamsTrait;

    public Summoner $me;

    public $other;

    public $version;

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
        $stats = ['me' => null, 'other' => null];
        if ($this->other != null) {
            $details = $this->me->versus($this->other, $this->filters)->filter(function ($detail) {
                return ($detail->me->won == $detail->other->won) == ($this->with == 'with');
            });
            $stats['me'] = new Stats($details->pluck('me'));
            $stats['other'] = new Stats($details->pluck('other'));
        } else {
            $details = collect([]);
        }

        return view('livewire.summoner-versus',
            [
                'details' => $this->paginate($details),
                'stats' => $stats,
            ]
        );
    }
}
