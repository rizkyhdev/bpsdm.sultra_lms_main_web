{{--
    @phpdoc
    Variabel:
    - $user: object{name,email,avatar_url,bio,phone,timezone,locale}
--}}
@extends('student.layouts.app')

@section('title', __('Edit Profile'))

@section('content')
    <a id="edit"></a>
    @include('student._breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'route' => 'student.dashboard'],
        ['label' => 'Profile', 'route' => 'student.profile.show'],
        ['label' => 'Edit'],
    ]])

    <x-student::card :title="__('Update your profile')">
        <form method="POST" action="{{ route('student.profile.update') }}" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-student::input name="name" :label="__('Name')" :value="$user->name" required />
                <x-student::input name="email" type="email" :label="__('Email')" :value="$user->email" required />
                <x-student::input name="phone" :label="__('Phone')" :value="$user->phone" />
                <x-student::select name="timezone" :label="__('Timezone')" :value="$user->timezone" :options="array_combine(timezone_identifiers_list(), timezone_identifiers_list())" placeholder="__('Select timezone')" />
                <x-student::select name="locale" :label="__('Locale')" :value="$user->locale" :options="['en' => 'English', 'id' => 'Bahasa Indonesia']" placeholder="__('Select locale')" />
                <div>
                    <label for="avatar" class="block text-sm font-medium">{{ __('Avatar') }}</label>
                    <input id="avatar" name="avatar" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-100 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-gray-600" />
                    @error('avatar')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <x-student::textarea name="bio" :label="__('Bio')" :value="$user->bio" rows="5" />
            </div>

            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('student.profile.show') }}" class="px-3 py-2 rounded-md border text-sm hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Cancel') }}</a>
                <button type="submit" class="px-3 py-2 rounded-md bg-indigo-600 text-white text-sm hover:bg-indigo-700">{{ __('Save changes') }}</button>
            </div>
        </form>
    </x-student::card>
@endsection


