{{-- Komponen Textarea (Bahasa) --}}
@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'rows' => 4,
    'helper' => null,
])
@php($id = $attributes->get('id') ?? $name)
<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium">{!! __($label) !!}</label>
    @endif
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}" @required($required)
        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
    @if($helper)
        <p class="text-xs text-gray-500">{!! __($helper) !!}</p>
    @endif
</div>


