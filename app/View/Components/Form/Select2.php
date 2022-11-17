<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Select2 extends Component
{
    public function __construct(
        public string $model,
        public array $options,
        public string $placeholder,
        public bool $nullable = false,
        public bool $multiple = false,
        public bool $optGroup = false,
    ) {
    }
    public function render()
    {
        return view('components.form.select2');
    }
}
