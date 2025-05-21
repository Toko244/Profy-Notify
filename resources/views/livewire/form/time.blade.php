<div class="form-group {{ $class }}">
    <label>{{ $label }}</label>
    <input
        type="time"
        class="form-control"
        {{ $required ? 'required' : '' }}
        wire:model.live="value"
        placeholder="{{ $placeholder }}"
        name="{{ $name }}"
    >
</div>
