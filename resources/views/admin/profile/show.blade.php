@extends('layouts.admin')

@section('title', __('Admin Profile'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Profile') }}</li>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
        <x-admin.card class="lg:col-span-1" :title="__('Your Info')">
            <div class="flex flex-col items-center text-center">
                <img src="{{ $user->avatar_url }}" alt="{{ __('Avatar') }}" class="h-24 w-24 rounded-full object-cover">
                <h1 class="mt-3 text-lg font-bold">{{ $user->name }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</p>
                <p class="mt-2 text-sm">{{ $user->bio }}</p>
                <a href="{{ route('admin.profile.edit') }}" class="mt-3 inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">{{ __('Edit Profile') }}</a>
            </div>
        </x-admin.card>

        <x-admin.card class="lg:col-span-2" :title="__('Details')">
            <dl class="text-sm space-y-2">
                <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-300">{{ __('Role') }}</dt><dd class="capitalize">{{ $user->role ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-300">{{ __('Phone') }}</dt><dd>{{ $user->phone ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-300">{{ __('Timezone') }}</dt><dd>{{ $user->timezone ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-300">{{ __('Locale') }}</dt><dd>{{ $user->locale ?? '-' }}</dd></div>
            </dl>
        </x-admin.card>
    </div>
@endsection
