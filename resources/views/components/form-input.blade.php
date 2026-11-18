@props(['name', 'label' => null, 'type' => 'text', 'value' => '', 'placeholder' => '', 'required' => false, 'disabled' => false])

<div>
    @if($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-bold text-[#141414]">
            {{ $label }} @if($required)<span class="text-[#991B1B]">*</span>@endif
        </label>
    @endif
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @class([
            'nb-input w-full',
            'border-[#991B1B] bg-[#FEE2E2]' => $errors->has($name),
            'cursor-not-allowed bg-[#F1F5F9] text-[#64748B]' => $disabled,
        ])
    >
    @error($name)
        <p class="mt-1.5 text-sm font-medium text-[#991B1B]">{{ $message }}</p>
    @enderror
</div>
