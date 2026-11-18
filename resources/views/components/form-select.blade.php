@props(['name', 'label' => null, 'value' => '', 'options' => [], 'placeholder' => 'Pilih opsi...', 'required' => false, 'disabled' => false])

<div>
    @if($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-bold text-[#141414]">
            {{ $label }} @if($required)<span class="text-[#991B1B]">*</span>@endif
        </label>
    @endif
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @class([
            'nb-input w-full',
            'border-[#991B1B] bg-[#FEE2E2]' => $errors->has($name),
            'cursor-not-allowed bg-[#F1F5F9] text-[#64748B]' => $disabled,
        ])
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected(old($name, $value) == $optValue)>
                {{ $optLabel }}
            </option>
        @endforeach
    </select>
    @error($name)
        <p class="mt-1.5 text-sm font-medium text-[#991B1B]">{{ $message }}</p>
    @enderror
</div>
