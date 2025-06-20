@php
    $selectedValues = is_array($selected) ? $selected : (array) $selected;
@endphp

<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <select class="form-control" name="{{ $name }}[]" multiple wire:model.live="selected">
        @foreach ($options as $option)
            <option value="{{ $option->{$option_value} }}"
                {{ in_array($option->{$option_value}, $selectedValues) ? 'selected' : '' }}>
                {{ $option->{$option_label} }}
            </option>
        @endforeach
    </select>
    @error($name) <span class="text-danger">{{ $message }}</span> @enderror
</div>
