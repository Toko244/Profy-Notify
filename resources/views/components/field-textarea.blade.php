<div class="form-group {{ $class }}">
    <label>{{ $label }}</label>
    <textarea class="summernote form-control" required={{ $required }} style="min-height: 400px"
        name="{{ $name }}">{{ isset($value) ? $value : '' }}</textarea>
</div>
