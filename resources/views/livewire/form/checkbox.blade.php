<div class="form-group {{ $class }}">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" class="form-check-input" id="{{ $id }}" value="1" {{ $value == 1 ? 'checked' : '' }}>
    <label class="form-check-label" for="{{ $id }}">{{ $label }}</label>
</div>
