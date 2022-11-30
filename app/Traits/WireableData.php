<?php

namespace App\Traits;

trait WireableData
{
    public function toLivewire(): array
    {
        return $this->toArray();
    }

    public static function fromLivewire($value): static
    {
        return self::withoutMagicalCreationFrom($value);
    }

    public static function fromString($value): static
    {
        return self::withoutMagicalCreationFrom(json_decode($value, true));
    }
}
