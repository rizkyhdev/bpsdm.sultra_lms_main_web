@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'options' => [],
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'help' => null,
])

@php($inputId = $id ?? $name ?? uniqid('select_'))

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium">{{ $label }} @if($required)<span class="text-red-600">*</span>@endif</label>
    @endif
    <select id="{{ $inputId }}" name="{{ $name }}" @class([
        'block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm',
        'focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500',
        'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500' => $errors->has($name),
    ]) @if($required) required @endif>
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected(old($name, $value) == $optValue)>{{ $optLabel }}</option>
        @endforeach
    </select>
    @if($help)
        <p class="text-xs text-gray-500">{{ $help }}</p>
    @endif
    @error($name)
        <p class="text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>


