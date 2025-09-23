{{-- Komponen Select (Bahasa) - Anonymous component wrapper untuk <x-student.select> --}}
@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'helper' => null,
])
@php($id = $attributes->get('id') ?? $name)
<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium">{!! __($label) !!}</label>
    @endif
    <select id="{{ $id }}" name="{{ $name }}" @required($required)
        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @if($placeholder)
            <option value="">{{ __($placeholder) }}</option>
        @endif
        @foreach($options as $key => $text)
            <option value="{{ $key }}" @selected(old($name, $value) == $key)>{{ __($text) }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
    @if($helper)
        <p class="text-xs text-gray-500">{!! __($helper) !!}</p>
    @endif
</div>


