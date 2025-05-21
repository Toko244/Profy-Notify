<div class="form-group {{ $class }}">
    <label>{{ $label }}</label>
    <textarea class="form-control" {{ $required ? 'required' : '' }} wire:model.live="value" placeholder="{{ $placeholder }}"
        name="{{ $name }}">
    </textarea>
</div>
