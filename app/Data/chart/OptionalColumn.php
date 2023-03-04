<?php

namespace App\Data\chart;

class OptionalColumn extends  \Spatie\LaravelData\Data
{

        public function __construct(
            public string $type,
            public string $role,
            public array $data,
            public array $p=['html' => true],

        )
        {
        }
}
