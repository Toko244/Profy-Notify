<?php

namespace App\Livewire\Form;

use Livewire\Component;

class Select extends Component
{
    public $identifier = null;
    public $label;
    public $name;
    public $options;
    public $selected;
    public $class = '';
    public $listener = '';
    public $nullable = false;
    public $option_value = 'value';
    public $option_label = 'value';

    public function change()
    {
        if ($this->listener) {
            if ($this->identifier) {
                $this->dispatch($this->listener, [
                    'identifier' => $this->identifier,
                    'value' => $this->selected
                ]);
            }else{
                $this->dispatch($this->listener, $this->selected);
            }
        }
    }


    public function render()
    {
        return view('livewire.form.select',[
            'label' => $this->label,
            'name' => $this->name,
            'options' => $this->options,
            'selected' => $this->selected,
            'class' => $this->class,
            'listener' => $this->listener,
        ]);
    }
}
