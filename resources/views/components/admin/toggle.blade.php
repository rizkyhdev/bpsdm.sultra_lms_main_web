@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'checked' => false,
    'help' => null,
])

@php($inputId = $id ?? $name ?? uniqid('toggle_'))

<div class="flex items-center justify-between">
    <div class="flex flex-col">
        @if($label)
            <label for="{{ $inputId }}" class="text-sm font-medium">{{ $label }}</label>
        @endif
        @if($help)
            <p class="text-xs text-gray-500">{{ $help }}</p>
        @endif
    </div>
    <button type="button" x-data="{ on: {{ old($name, $checked) ? 'true' : 'false' }} }" x-on:click="on = !on; $refs.input.checked = on" :aria-pressed="on.toString()" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none border border-gray-300 dark:border-gray-700" :class="on ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'">
        <span class="sr-only">{{ $label }}</span>
        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="on ? 'translate-x-6' : 'translate-x-1'"></span>
        <input x-ref="input" type="checkbox" id="{{ $inputId }}" name="{{ $name }}" class="sr-only" @checked(old($name, $checked)) />
    </button>
    @error($name)
        <p class="text-xs text-red-600 ml-3">{{ $message }}</p>
    @enderror
</div>


