<?php

namespace App\Livewire\Form;

use Livewire\Component;

class Time extends Component
{
    public $label;
    public $name;
    public $value = '';
    public $required = false;
    public $placeholder = '';
    public $class = '';

    public function render()
    {
        return view('livewire.form.time', [
            'label' => $this->label,
            'name' => $this->name,
            'value' => $this->value,
            'required' => $this->required,
            'placeholder' => $this->placeholder,
            'class' => $this->class,
        ]);
    }
}
