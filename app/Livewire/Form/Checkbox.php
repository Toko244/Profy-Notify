<?php

namespace App\Livewire\Form;

use Livewire\Component;

class Checkbox extends Component
{
    public $name;
    public $label;
    public $value = '';
    public $id = '';
    public $class = '';

    public function render()
    {
        return view('livewire.form.checkbox', [
            'name' => $this->name,
            'label' => $this->label,
            'value' => $this->value,
            'id' => $this->id,
            'class' => $this->class,
        ]);
    }
}
