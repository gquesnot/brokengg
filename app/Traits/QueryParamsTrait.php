<?php

namespace App\Traits;

trait QueryParamsTrait
{
    public function getParamsUrl()
    {
        $result = [];
        if ($this->filters != null) {
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

        return '?'.http_build_query($result);
    }
}
