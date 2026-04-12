@extends('layouts.admin')

@section('title', __('Edit Admin Profile'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.profile.show') }}">{{ __('Profile') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
@endsection

@section('content')
    <div class="mt-4 max-w-4xl mx-auto">
        <x-admin.card :title="__('Update your profile')">
            <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin.input name="name" :label="__('Name')" :value="old('name', $user->name)" required />
                    <x-admin.input name="email" type="email" :label="__('Email')" :value="old('email', $user->email)" required />
                    <x-admin.input name="phone" :label="__('Phone')" :value="old('phone', $user->phone)" />
                    <x-admin.select name="timezone" :label="__('Timezone')" :value="old('timezone', $user->timezone)" :options="array_combine(timezone_identifiers_list(), timezone_identifiers_list())" :placeholder="__('Select timezone')" />
                    <x-admin.select name="locale" :label="__('Locale')" :value="old('locale', $user->locale ?? 'id')" :options="['en' => 'English', 'id' => 'Bahasa Indonesia']" :placeholder="__('Select locale')" />
                    
                    <div class="col-span-1 md:col-span-1">
                        <label for="avatar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Avatar (Optional)') }}</label>
                        <input id="avatar" name="avatar" type="file" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" />
                        @error('avatar')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        @if($user->avatar)
                            <p class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i> Current avatar is set.</p>
                        @endif
                    </div>
                    
                    <div class="col-span-1 md:col-span-2">
                        <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Bio (Optional)') }}</label>
                        <textarea id="bio" name="bio" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200">{{ old('bio', $user->bio ?? '') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6">
                    <a href="{{ route('admin.profile.show') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-save mr-2"></i> {{ __('Save changes') }}
                    </button>
                </div>
            </form>
        </x-admin.card>
    </div>
@endsection
