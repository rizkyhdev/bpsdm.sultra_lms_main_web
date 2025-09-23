{{-- Komponen Input (Bahasa) --}}
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'helper' => null,
])
@php($id = $attributes->get('id') ?? $name)
<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium">{!! __($label) !!}</label>
    @endif
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}" @required($required)
        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
    @if($helper)
        <p class="text-xs text-gray-500">{!! __($helper) !!}</p>
    @endif
</div>


