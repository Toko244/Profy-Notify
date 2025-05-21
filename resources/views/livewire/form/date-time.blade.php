<div class="form-group {{ $class }}">
    <label>{{ $label }}</label>
    <input
        type="datetime-local"
        class="form-control"
        {{ $required ? 'required' : '' }}
        wire:model.live="value"
        placeholder="{{ $placeholder }}"
        name="{{ $name }}"
    >
</div>
