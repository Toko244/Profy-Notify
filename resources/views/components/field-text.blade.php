<div class="form-group {{ $class }}">
    <label>{{ $label }}</label>
    <input
        type="text"
        class="form-control"
        required={{ $required }}
        value="{{ isset($value) ? $value : '' }}"
        placeholder="{{ $placeholder }}"
        name="{{ $name }}"
        {{ isset($disabled) && $disabled ? 'disabled' : '' }}
    >
</div>
