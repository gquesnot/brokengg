<?php

namespace App\Http\Livewire\Form;

use Livewire\Component;

class Select2 extends Component
{

    public string $model;
    public array $options;
    public bool $nullable = false;
    public bool $multiple = false;

    public function mount(string $model, array $options, bool $nullable = false, bool $multiple = false)
    {
        $this->model = $model;
        $this->options = $options;
        $this->nullable = $nullable;
        $this->multiple = $multiple;
    }

    public function render()
    {
        return view('livewire.form.select2');
    }
}