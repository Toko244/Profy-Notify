<div>
    <div class="form-group" style="position: relative">
        <label for="{{ $name }}">{{ $label }}</label>
        <select class="form-control" name="{{ $name }}" wire:change="change" wire:model.change="selected">
            @if (isset($nullable) && $nullable)
                <option value=""></option>
            @endif
            @foreach ($options as $key => $value)
                <option value="{{ ${$option_value} }}">{{ ${$option_label} }}</option>
            @endforeach
        </select>
        <span style="position: absolute; bottom: 6px; right: 10px"><i class="bi bi-caret-down-fill"></i></span>
        @error($name) <span class="text-danger">{{ $message }}</span> @enderror
    </div>
</div>
