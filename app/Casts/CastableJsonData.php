<?php

namespace App\Casts;

use Spatie\LaravelData\Data;

class CastableJsonData
{
    public function __construct(
        /** @var class-string<Data> $dataClass */
        protected string $dataClass
    ) {
    }
}
