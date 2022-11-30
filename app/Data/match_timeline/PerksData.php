<?php

namespace App\Data\match_timeline;

use Spatie\LaravelData\Data;

class PerksData extends Data
{
    public function __construct(
        public int $offense,
        public int $defense,
        public int $flex,
    ) {
    }
}
