<?php

namespace App\View\Components;

use App\Enums\FLashEnum;
use Illuminate\View\Component;
use Illuminate\View\View;

class Flash extends Component
{
    public function __construct(
        public FLashEnum $type,
    ) {
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return  View
     */
    public function render(): View
    {
        return view('components.flash');
    }
}
