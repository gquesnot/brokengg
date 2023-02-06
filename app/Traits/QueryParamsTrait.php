<?php

namespace App\Traits;

use function PHPUnit\Framework\isEmpty;

trait QueryParamsTrait
{
    public function getParamsUrl(): string
    {
        $result = [];
        if ($this->filters) {
            foreach ($this->filters as $key => $value) {
                if ($value != null) {
                    $result[$key] = $value;
                }
                if ($key == 'filter_encounters') {
                    if ($value) {
                        $result[$key] = 1;
                    }
                }
            }
        }
        if (!isEmpty($result) > 0) {
            return '?' . http_build_query($result);
        }
        return "";
    }
}
