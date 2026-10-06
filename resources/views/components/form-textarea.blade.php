@props(['name', 'label' => null, 'value' => '', 'placeholder' => '', 'required' => false, 'disabled' => false, 'rows' => 3])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-[#1E293B] mb-2">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <textarea 
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-300 transition-all resize-none
            @error($name) border-red-500 bg-red-50 @else border-[#E2E8F0] @enderror
            @if($disabled) bg-gray-50 text-gray-500 cursor-not-allowed @endif"
    >{{ old($name, $value) }}</textarea>
    @error($name) 
        <p class="text-sm text-red-500 mt-2 flex items-center gap-1">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            {{ $message }}
        </p>
    @enderror
</div>
