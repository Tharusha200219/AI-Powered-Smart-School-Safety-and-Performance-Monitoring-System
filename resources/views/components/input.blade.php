<div class="mt-2">
    @if ($title != '')
        <small class="text-xs">{{ $title }} @if ($isRequired)
                <span class="text-danger text-md">*</span>
            @endif
        </small>
    @endif


    <div class="input-group input-group-outline my-1">
        @if ($type === 'select')
            <select name="{{ $name }}" id="{{ $name }}"
                class="form-control {{ $class ?? '' }} @error($name) is-invalid @enderror" {{ $attr }}
                @if ($isRequired) required @endif>
                <option value="">{{ $placeholder ?? 'Select an option' }}</option>
                @foreach ($options as $key => $label)
                    <option value="{{ $key }}" @selected($value == $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        @elseif ($type === 'textarea')
            <textarea name="{{ $name }}" id="{{ $name }}"
                class="form-control {{ $class ?? '' }} @error($name) is-invalid @enderror" {{ $attr }}
                @if ($isRequired) required @endif placeholder="{{ $placeholder }}">{{ old($name, $value) }}</textarea>
        @else
            <input value="{{ $value }}" type="{{ $type }}" name="{{ $name }}"
                id="{{ $name }}" class="form-control {{ $class ?? '' }} @error($name) is-invalid @enderror"
                {{ $attr }} @if ($isRequired) required @endif
                @if ($type === 'checkbox' || $type === 'radio') value="1"
                @checked(old($name)) @endif>

            @if ($type === 'checkbox' || $type === 'radio')
                <label class="form-check-label" for="{{ $name }}">
                    {{ $title ?? ucwords(str_replace('_', ' ', $name)) }}
                </label>
            @endif
        @endif
    </div>

    @include('admin.layouts.form-error', ['input' => $name])

    @if ($placeholder && $type !== 'checkbox' && $type !== 'radio' && $type !== 'select')
        <small class="text-primary ms-2">{{ $placeholder }}</small>
    @endif
</div>
