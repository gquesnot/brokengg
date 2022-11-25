<?php

namespace App\Data;

use App\Models\Matche;
use App\Traits\LivewireDataTrait;
use Livewire\Wireable;

class MatchData extends \Spatie\LaravelData\Data implements Wireable
{
    use LivewireDataTrait;

    public function __construct(
        public Matche $match,
        public array $teams,
    ){
    }

}
