@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'value' => null,
    'rows' => 4,
    'required' => false,
    'help' => null,
])

@php($inputId = $id ?? $name ?? uniqid('textarea_'))

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium">{{ $label }} @if($required)<span class="text-red-600">*</span>@endif</label>
    @endif
    <textarea id="{{ $inputId }}" name="{{ $name }}" rows="{{ $rows }}" @class([
        'block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm',
        'focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500',
        'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500' => $errors->has($name),
    ]) @if($required) required @endif>{{ old($name, $value) }}</textarea>
    @if($help)
        <p class="text-xs text-gray-500">{{ $help }}</p>
    @endif
    @error($name)
        <p class="text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>


