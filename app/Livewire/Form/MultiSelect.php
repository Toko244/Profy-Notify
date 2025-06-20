<?php

namespace App\Livewire\Form;

use Livewire\Component;

class MultiSelect extends Component
{
    public $identifier = null;
    public $label;
    public $name;
    public $options;
    public $selected = [];
    public $class = '';
    public $listener = '';
    public $option_value = 'value';
    public $option_label = 'value';

    public function updatedSelected()
    {
        $selectedValue = is_array($this->selected) ? $this->selected : (array) $this->selected;

        if ($this->listener === 'changeType') {
            $this->dispatch('changeType', $selectedValue);
            $this->dispatch('console.log', ['message' => 'MultiSelect dispatching:', 'value' => $selectedValue]);
        } elseif ($this->listener) {
            if ($this->identifier) {
                $this->dispatch($this->listener, [
                    'identifier' => $this->identifier,
                    'value' => $selectedValue
                ]);
            } else {
                $this->dispatch($this->listener, $selectedValue);
            }
        }
    }

    public function render()
    {
        return view('livewire.form.multi-select', [
            'label' => $this->label,
            'name' => $this->name,
            'options' => $this->options,
            'selected' => $this->selected,
            'class' => $this->class,
            'listener' => $this->listener,
        ]);
    }
}
