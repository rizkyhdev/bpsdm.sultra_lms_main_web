{{--
    @phpdoc
    Variabel:
    - $settings: object{theme, timezone, locale, email_notifications:boolean}
--}}
@extends('student.layouts.app')

@section('title', __('Settings'))

@section('content')
    @include('student._breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'route' => 'student.dashboard'],
        ['label' => 'Settings'],
    ]])

    <x-student::card :title="__('Preferences')">
        <form method="POST" action="{{ route('student.settings.index') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-student::select name="theme" :label="__('Theme')" :value="$settings->theme ?? 'light'" :options="['light' => 'Light', 'dark' => 'Dark', 'system' => 'System']" />
                <x-student::select name="timezone" :label="__('Timezone')" :value="$settings->timezone ?? config('app.timezone')" :options="array_combine(timezone_identifiers_list(), timezone_identifiers_list())" placeholder="__('Select timezone')" />
                <x-student::select name="locale" :label="__('Locale')" :value="$settings->locale ?? app()->getLocale()" :options="['en' => 'English', 'id' => 'Bahasa Indonesia']" />
                <div class="flex items-center gap-2 mt-6">
                    <input id="email_notifications" name="email_notifications" type="checkbox" value="1" @checked(old('email_notifications', (int)($settings->email_notifications ?? 0)) == 1) class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500">
                    <label for="email_notifications" class="text-sm">{{ __('Email notifications') }}</label>
                </div>
                @error('email_notifications')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="submit" class="px-3 py-2 rounded-md bg-indigo-600 text-white text-sm hover:bg-indigo-700">{{ __('Save settings') }}</button>
            </div>
        </form>
    </x-student::card>
@endsection


