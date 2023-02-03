<?php

namespace App\Data;

use Livewire\Wireable;

class FiltersData extends \Spatie\LaravelData\Data implements Wireable
{
    use \Spatie\LaravelData\Concerns\WireableData;

    public function __construct(
        public ?string $date_start = null,
        public ?string $date_end = null,
        public ?int $champion = null,
        public ?int $queue = null,
        public ?bool $filter_encounters = null,
    ) {
        if (! $this->filter_encounters) {
            $this->filter_encounters = null;
        }
    }

    public function clear_empty()
    {
        foreach ($this->toArray() as $key => $value) {
            if ($value == '') {
                $this->$key = null;
            }
        }
    }
}
