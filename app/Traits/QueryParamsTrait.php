<?php

namespace App\Traits;

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
        if (! empty($result) > 0) {
            return '?'.http_build_query($result);
        }

        return '';
    }
}
