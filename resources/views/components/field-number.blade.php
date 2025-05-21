<div class="form-group {{ $class }}">
    <label>{{ $label }}</label>
    <input type="number" class="form-control" required={{ $required }} value="{{ isset($value) ? $value : '' }}" placeholder="{{ $placeholder }}" name="{{ $name }}">
</div>
